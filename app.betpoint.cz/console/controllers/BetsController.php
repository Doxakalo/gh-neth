<?php

namespace console\controllers;

use Codeception\Lib\Console\Message;
use common\models\AppMonitor;
use common\models\SportMatchResult;
use common\models\Transaction;
use Exception;
use Yii;
use yii\console\Controller;

use function PHPUnit\Framework\throwException;

class BetsController extends Controller
{

	public function actionEvaluate()
	{
		$matchResultsToEvaluate = SportMatchResult::getNotEvaluatedMatchResults();

		$allDataEvaluated = true;
		foreach ($matchResultsToEvaluate as $matchResult) {
			$result = $matchResult->result;
			$match = $matchResult->sportMatch;
			$matchOdds = $match->odds;

			try {
				$transaction = \Yii::$app->db->beginTransaction();

				foreach ($matchOdds as $matchOdd) {
					$oddBetType = $matchOdd->oddBetType;

					if (!empty($matchOdd->userBets)) {
						$this->customStdout("Number of odds: " . count($matchOdd->userBets) . " for match result ID {$matchResult->id} with odd type {$oddBetType->name}.");
						foreach ($matchOdd->userBets as $userBet) {
							$potentialWin = $userBet->amount * $userBet->odd;
							$currentStatus = $userBet->status;
							// EVALUATION OF BET
							$evaluatedBet = $this->evaluateBet(
								$oddBetType->alias,
								$matchOdd->name,
								$result["home"],
								$result["away"]
							);
							$this->customStdout("Evaluating bet for user ID {$userBet->user_id} with bet type {$oddBetType->name} and odd {$matchOdd->name}.");
							$this->customStdout($evaluatedBet ? "Bet won." : "Bet lost.");
							$lastBetTransaction = Transaction::getLastTransactionBetReleated($userBet->id);
							$reevalationAmount = (empty($lastBetTransaction) ? 0 : ($lastBetTransaction->amount * -1));

							if ($evaluatedBet === true && $currentStatus !== $userBet::STATUS_WIN) {
								$this->customStdout("Bet won, processing win transaction.\n");
								$winTransaction = new Transaction();
								$winTransaction->user_id = $userBet->user_id;
								$winTransaction->type = ($matchResult->user_id === null ? Transaction::TYPE_WIN : Transaction::TYPE_REEVALUATION);
								$winTransaction->amount = $potentialWin;
								$winTransaction->user_bet_id = $userBet->id;
								$winTransaction->match_result_id = $matchResult->id;
								$winTransaction->setActionLabel();
								$winTransaction->setDescriptionLabel();
				
								if (!($winTransaction->validate() && $winTransaction->save())) {
									$errMessage = "Failed to save win transaction for user bet ID {$userBet->id}";
									$this->customStderr($errMessage, [$winTransaction->getErrors()]);

									$transaction->rollBack();
									throw new Exception($errMessage);
								}
							} else if ($evaluatedBet === false && $currentStatus !== $userBet::STATUS_LOSS && !empty($reevalationAmount)) {
								$this->customStdout("Bet lost, processing lose return transaction.");
								$this->customStdout("Results: Home: {$result['home']}, Away: {$result['away']}, Odd: {$matchOdd->name}, Bet Type: {$oddBetType->name}");
								$loseReturnTransaction = new Transaction();
								$loseReturnTransaction->user_id = $userBet->user_id;
								$loseReturnTransaction->type = Transaction::TYPE_REEVALUATION;
								$loseReturnTransaction->amount = $reevalationAmount;
								$loseReturnTransaction->user_bet_id = $userBet->id;
								$loseReturnTransaction->match_result_id = $matchResult->id;
								$loseReturnTransaction->setActionLabel();
								$loseReturnTransaction->setDescriptionLabel();

								if (!($loseReturnTransaction->validate() && $loseReturnTransaction->save())) {
									$errMessage = "Failed to save lose return transaction for user bet ID {$userBet->id}";
									$this->customStderr($errMessage, [$loseReturnTransaction->getErrors()]);

									$transaction->rollBack();
									throw new Exception($errMessage);
								}
							}

							$userBet->match_result_id = $matchResult->id;
							$userBet->status = $evaluatedBet ? $userBet::STATUS_WIN : $userBet::STATUS_LOSS;

							if (!($userBet->validate() && $userBet->save())) {
								$errMessage = "Failed to save user bet ID {$userBet->id} with status {$userBet->status}";
								$this->customStderr($errMessage, [$userBet->getErrors()]);

								$transaction->rollBack();
								throw new Exception($errMessage);
							}
						}
					}
				}

				$matchResult->evaluated = 1;
				if (!($matchResult->validate() && $matchResult->save())) {
					$errMessage = "Failed to save match result ID {$matchResult->id} as evaluated.";
					$this->customStderr($errMessage, [$matchResult->getErrors()]);

					$transaction->rollBack();
					throw new Exception($errMessage);
				}
				$transaction->commit();
			} catch (Exception $e) {
				$allDataEvaluated = false;
				$errMessage = "Error evaluating match result ID {$matchResult->id}: " . $e->getMessage();
				$this->customStderr($errMessage);
			}

	
			$this->customStdout("Match result ID {$matchResult->id} evaluated successfully.");
		}
		if ($allDataEvaluated) {
			AppMonitor::updateStatus("BETS_EVALUATE");
		}
	}

	function evaluateBet($betType, $oddName, int $homeGoals, int $awayGoals): bool
	{
		$totalGoals = $homeGoals + $awayGoals;

		switch ($betType) {
			case 'match-winner':
				switch ($oddName) {
					case 'Home':
						return $homeGoals > $awayGoals;
					case 'Away':
						return $homeGoals < $awayGoals;
					case 'Draw':
						return $homeGoals === $awayGoals;
					default:
						throw new Exception("Unknown odd name for Match Winner: $oddName");
				}
			case 'home-away':
				switch ($oddName) {
					case 'Home':
						return $homeGoals > $awayGoals;
					case 'Away':
						return $homeGoals < $awayGoals;
					default:
						throw new Exception("Unknown odd name for Home/Away: $oddName");
				}

			case 'over-under':
				if (preg_match('/^(Over|Under)\s+(\d+\.?\d*)$/', $oddName, $matches)) {
					$threshold = (float) $matches[2];
					return $matches[1] === 'Over' ? $totalGoals > $threshold : $totalGoals < $threshold;
				}
				throw new Exception("Invalid Over/Under format: $oddName");

			case 'total-home':
				if (preg_match('/^(Over|Under)\s+(\d+\.?\d*)$/', $oddName, $matches)) {
					$threshold = (float) $matches[2];
					return $matches[1] === 'Over' ? $homeGoals > $threshold : $homeGoals < $threshold;
				}
				throw new Exception("Invalid Total - Home format: $oddName");

			case 'total-away':
				if (preg_match('/^(Over|Under)\s+(\d+\.?\d*)$/', $oddName, $matches)) {
					$threshold = (float) $matches[2];
					return $matches[1] === 'Over' ? $awayGoals > $threshold : $awayGoals < $threshold;
				}
				throw new Exception("Invalid Total - Away format: $oddName");

			case 'result-total-goals':
				// Formát: "Home/Over 2.5", "Draw/Under 1.5", "Away/Over 3.5"
				if (preg_match('/^(Home|Draw|Away)\/(Over|Under)\s+(\d+\.?\d*)$/', $oddName, $matches)) {
					$result = $matches[1];
					$overUnder = $matches[2];
					$threshold = (float) $matches[3];

					// Nejprv vyhodnotíme výsledek zápasu
					$resultCorrect = false;
					switch ($result) {
						case 'Home':
							$resultCorrect = $homeGoals > $awayGoals;
							break;
						case 'Draw':
							$resultCorrect = $homeGoals === $awayGoals;
							break;
						case 'Away':
							$resultCorrect = $homeGoals < $awayGoals;
							break;
					}

					// Pak vyhodnotíme total goals
					$totalCorrect = $overUnder === 'Over' ? $totalGoals > $threshold : $totalGoals < $threshold;

					// Obě podmínky musí být splněny
					return $resultCorrect && $totalCorrect;
				}
				throw new Exception("Invalid Result/Total Goals format: $oddName");

			case 'total-hits':
				// Speciální případ pro baseball - předpokládáme stejnou logiku jako Over/Under
				if (preg_match('/^(Over|Under)\s+(\d+\.?\d*)$/', $oddName, $matches)) {
					$threshold = (float) $matches[2];
					return $matches[1] === 'Over' ? $totalGoals > $threshold : $totalGoals < $threshold;
				}
				throw new Exception("Invalid Total Hits format: $oddName");

			default:
				throw new Exception("Unknown bet type: $betType");
		}
	}


	private function customStdout($message, $logData = [])
	{
		$this->stdout(date('Y-m-d H:i:s') . "\t" . $message . "\n");
		Yii::info((empty($logData) ? $message : [
			"message" => $message,
			"data" => $logData
		]), 'bets-evaluate');
	}

	private function customStderr($message , $logData = [])
	{
		$this->stderr(date('Y-m-d H:i:s') . "\t" . $message . "\n", \yii\helpers\Console::FG_RED);
		Yii::error((empty($logData) ? $message : [
			"message" => $message,
			"data" => $logData
		]), 'bets-evaluate');
	}
}

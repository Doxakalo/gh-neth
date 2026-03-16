<?php

namespace console\controllers;

use Codeception\Lib\Console\Message;
use common\models\AppMonitor;
use common\models\SportMatch;
use common\models\SportMatchResult;
use common\models\Transaction;
use common\models\UserBet;
use DateTime;
use Directory;
use DirectoryIterator;
use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;

use RecursiveIteratorIterator;
use Yii;
use yii\console\Controller;

use function PHPUnit\Framework\throwException;

class ClearDataController extends Controller
{
	public function actionDatabaseData()
	{
		$cleanupConfiguration = Yii::$app->params["unusedData"] ?? [];
		$deleteAfterDays = $cleanupConfiguration["deleteAfter"] ?? 1;
	
		$date = new DateTime();
		$date->modify('-'. $deleteAfterDays.' days');

		$query = SportMatch::find()->where(["evaluated" => 1])->orWhere(['<', 'match_start', $date->getTimestamp()]);

		$query->with([
			'odds.userBets',
			'sportMatchResults'
		]);

		// LIMIT is set to avoid memory issues
		$matches = $query->limit(2000)->all();

		foreach($matches as $match){
	
			try {
				$transaction = \Yii::$app->db->beginTransaction();
		
				$deleteMatch = true;
				foreach ($match->odds as $odd) {
					if(empty($odd->userBets)){
						if($odd->delete() === false){
							$transaction->rollBack();
							throw new Exception("Failed to delete odd with ID {$odd->id} for match ID {$match->id}" . "\n");
						} else {
							$this->stdout("Deleted odd with ID {$odd->id} for match ID {$match->id} due there are not bets from user" . "\n");
						}
					} else {
						$this->stdout("There are existing odds for match ID {$match->id}, skipping deletion of this match." . "\n");
						$deleteMatch = false;
						continue;
					}
				}

				if($deleteMatch){
					foreach($match->sportMatchResults as $matchResult) {
						if ($matchResult->delete() === false) {
							$transaction->rollBack();
							throw new Exception("Failed to delete match result with ID {$matchResult['id']} for match ID {$match['id']}" . "\n");
						} else {
							$this->stdout("Deleted match result with ID {$matchResult['id']} for match ID {$match['id']}" . "\n");
						}
					}

					if ($match->delete() === false) {
						$transaction->rollBack();
						throw new Exception("Failed to delete match with ID {$match['id']}" . "\n");
					} else {
						$this->stdout("Deleted match with ID {$match['id']}" . "\n");
					}
				}

				$transaction->commit();
				AppMonitor::updateStatus("DELETE_OLD_DATA");
			} catch(Exception $e) {
				$this->stderr("Error while processing match ID {$match['id']}: " . $e->getMessage() . "\n");
			}
		}
	}

	public function actionClearLogs() {
		$pathCronLogs = realpath(__DIR__ . '/../../cron/logs');
		if (!is_dir($pathCronLogs)) {
			$this->stdout("Directory $pathCronLogs does not exist.\n");
			return;
		}

		$cleanupConfiguration = Yii::$app->params["cronLogs"] ?? [];
		$keepAliveDays = $cleanupConfiguration["keepAlive"] ?? 1;

		$now = time();

		$allDeleted = true;
		

		if ($pathCronLogs && is_dir($pathCronLogs)) {

			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($pathCronLogs, FilesystemIterator::SKIP_DOTS),
				RecursiveIteratorIterator::SELF_FIRST
			);

			$deleted = 0;

			foreach ($iterator as $file) {
				if ($file->isFile() && strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION)) === 'log') {
					if (@unlink($file->getPathname())) {
						echo "Smazán: " . $file->getPathname() . PHP_EOL;
						$deleted++;
					} else {
						echo "Nelze smazat: " . $file->getPathname() . PHP_EOL;
					}
				}
			}

			echo "Celkem smazáno .log souborů: $deleted" . PHP_EOL;

		} else {
			echo "Složka $pathCronLogs neexistuje nebo není přístupná.";
		}
		foreach (new DirectoryIterator($pathCronLogs) as $fileInfo) {
			if ($fileInfo->isDot() || !$fileInfo->isFile()) continue;

			$filePath = $fileInfo->getPathname();
			$fileMTime = $fileInfo->getMTime();


		
			if ($now - $fileMTime > ($keepAliveDays * 86400)) {
				if (unlink($filePath)) {
					$this->stdout("Deleted log file: $filePath\n");
				} else {
					$allDeleted = false;
					$this->stderr("Failed to delete log file: $filePath\n");
				}
			}
		}
		if($allDeleted){
			AppMonitor::updateStatus("DELETE_OLD_LOGS");
		}
	}

	public function actionDeleteFrequentedLogs() {
		$pathCronLogs = Yii::getAlias('@console/runtime/logs/cron/');
		if (!is_dir($pathCronLogs)) {
			$this->stdout("Directory $pathCronLogs does not exist.\n");
			return;
		}

		$cleanupConfiguration = Yii::$app->params["cronLogs"] ?? [];
		$keepAliveDays = $cleanupConfiguration["keepAlive"] ?? 1;

		$now = time();

		// Logy, které chceme mazat
		$frequentLogs = [
			'sync_latest_matches',
			'sync_bets_evaluate'
		];

		$dirIterator = new DirectoryIterator($pathCronLogs);
		foreach ($dirIterator as $fileInfo) {
			if ($fileInfo->isDot() || !$fileInfo->isFile()) continue;

			$fileName = $fileInfo->getFilename();
			$filePath = $fileInfo->getPathname();
			$fileMTime = $fileInfo->getMTime();

			// Jen frekventované logy
			$matchesFrequentLog = false;
			foreach ($frequentLogs as $pattern) {
				if (stripos($fileName, $pattern) !== false) {
					$matchesFrequentLog = true;
					break;
				}
			}

			if (!$matchesFrequentLog) continue;

			// Kontrola stáří souboru
			if ($now - $fileMTime > ($keepAliveDays * 86400)) {
				if (unlink($filePath)) {
					$this->stdout("Deleted log file: $filePath\n");
				} else {
					$allDeleted = false;
					$this->stderr("Failed to delete log file: $filePath\n");
				}
			}
		}

	}
}

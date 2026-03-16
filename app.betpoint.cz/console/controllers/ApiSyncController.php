<?php

namespace console\controllers;

use common\models\AppMonitor;
use common\models\Category;
use common\models\Odd;
use common\models\OddBetType;
use common\models\Season;
use common\models\Sport;
use common\models\SportMatch;
use common\models\SportMatchResult;
use Yii;
use yii\console\Controller;
use yii\helpers\VarDumper;
use common\services\ApiSports;
use common\services\Baseball;
use common\services\BaseSport;
use common\services\Basketball;
use common\services\Football;
use common\services\Handball;
use common\services\Hockey;
use common\services\Nfl;
use common\services\Rugby;
use common\services\Volleyball;
use DateTime;
use Exception;
use yii\di\Instance;

class ApiSyncController extends Controller
{
	// ========================================================================================================
	// Odds
	// ========================================================================================================

	/**
	 * This method will sync odds for all sports
	 * It will call specific methods for each sport to handle the syncing process
	 *
	 * @return void
	 */
	public function actionSyncOdds()
	{
		$this->customStdout("Start syncing odds for all sports \n");

		$sports = Sport::getAllSports();

		$allDataUpdated = true;
		foreach ($sports as $sport) {

			$alias = $sport["alias"];

			try {
				$sportClass = BaseSport::getSportClassByAlias($alias);

				$this->syncSportOdds($sport, new $sportClass);
			} catch (Exception $e) {
				$allDataUpdated = false;
				$message = sprintf("Error getting sport class for alias %s: %s", $alias, $e->getMessage());
				Yii::error($message, 'data-sync');
				$this->customStderr($message . "\n");
				continue;
			}
		}
		if ($allDataUpdated) {
			AppMonitor::updateStatus("SYNC_ODDS");
		}

		$this->customStdout("Finished syncing odds for all sports \n");
	}

	public function actionSyncSingleOdds($sport_alias, $sportName)
	{
		$this->customStdout("Start syncing odds for all sports \n");
		$id = Sport::findByAlias($sport_alias);
		$id = $id->id;

		$sport = ['alias' => $sport_alias, 'name' => $sportName, 'id' => $id ];

		$allDataUpdated = true;
		$alias = $sport["alias"];
			
			try {
				$sportClass = BaseSport::getSportClassByAlias($alias);
				$this->syncSportOdds($sport, new $sportClass);
			} catch (Exception $e) {
				$allDataUpdated = false;
				$message = sprintf("Error getting sport class for alias %s: %s", $alias, $e->getMessage());
				Yii::error($message, 'data-sync');
				$this->customStderr($message . "\n");
			}
		if ($allDataUpdated) {
			AppMonitor::updateStatus("SYNC_ODDS");
		}

		$this->customStdout("Finished syncing odds for all sports \n");
	}

	/**
	 * This method will update existing odds and add new odds based on data from API Sport
	 *
	 * @param array $sport (database record of sport)
	 * @param BaseSport $sportClass (instance of the sport class)
	 */
	public function syncSportOdds($sport, $sportClass)
	{
		try {
			$this->customStdout(sprintf("Start - %s", $sport["name"]));

			if (!is_subclass_of($sportClass, BaseSport::class)) {
				throw new \InvalidArgumentException("The provided sportClass must be a subclass of BaseSport.");
			}

			if ($sport["alias"] === 'nfl') {
				// NFL has different logic for syncing odds, so we skip the renewal process
				$this->customStdout("Custom logic for NFL.");

				$this->renewNflOdds($sport, $sportClass);


			} else {
				
				$this->renewOdds($sport, $sportClass);
			}

			$this->customStdout(sprintf("Finished - %s \n", $sport["name"]));
		} catch (\Exception $e) {
			$message = sprintf("Error syncing odds for sport: %s - %s \n", $sport["name"], $e->getMessage());
			Yii::error($message, 'data-sync');
			$this->customStderr($message . "\n");
		}
	}

	/**
	 * This method will renew odds for NFL sport
	 * It will call specific methods to handle the syncing process
	 *
	 * @param array $sport (database record of sport)
	 * @param BaseSport $sportClass (instance of the sport class)
	 */
	public function renewNflOdds($sport, $sportClass)
	{
		$sportId = $sport["id"];

		$enabledOddBetTypes = (new \yii\db\Query())
			->select(['id', 'id_vendor', 'name'])
			->from('odd_bet_type')
			->where(['sport_id' => 7])
			->andWhere(['IS NOT', 'alias', null])
			->all();

		$excludedStatuses = ['CANC','ABD','PST','POST','FT','AOT','AP','AWD','AW','WO'];

		$sportMatches = (new \yii\db\Query())
			->select([
				'id',
				'id_vendor',
				'match_start',
				new \yii\db\Expression('UNIX_TIMESTAMP(CURDATE()) AS today_start_timestamp'),
				new \yii\db\Expression('UNIX_TIMESTAMP(CURDATE() + INTERVAL 3 DAY) AS three_days_later_timestamp'),
			])
			->from('sport_match')
			->where(['sport_id' => 7])
			->andWhere(['NOT IN', 'status_name', $excludedStatuses])
			->andWhere(new \yii\db\Expression('match_start >= UNIX_TIMESTAMP(CURDATE())'))
			->andWhere(new \yii\db\Expression('match_start < UNIX_TIMESTAMP(CURDATE() + INTERVAL 3 DAY)'))
			->all();

		$sqlValues = [];

		foreach ($sportMatches as $match) {
			$matchVendorId = $match['id_vendor'];
			$matchId = $match['id'];

			foreach ($enabledOddBetTypes as $betType) {
				$betTypeVendorId = $betType['id_vendor'];
				$betTypeId = $betType['id'];

				try {
					$matchOdds = $sportClass->getOdds([
						"game" => $matchVendorId,
						"bet" => $betTypeVendorId
					]);

					$this->customStdout("API data for matchVendorId={$matchVendorId}, betTypeVendorId={$betTypeVendorId}:\n");
					$this->customStdout(print_r($matchOdds, true) . "\n");

				} catch (\Exception $e) {
					$this->customStdout("Error fetching odds for matchVendorId={$matchVendorId}, betTypeVendorId={$betTypeVendorId}: {$e->getMessage()}\n");
					continue;
				}

				if (empty($matchOdds)) continue;

				// --- Načtení existujících kurzů z DB ---
				$existingOdds = (new \yii\db\Query())
					->select(['name', 'odd_raw'])
					->from('odd')
					->where(['sport_match_id' => $matchId, 'odd_bet_type_id' => $betTypeId])
					->all();

				$existingMap = [];
				foreach ($existingOdds as $eo) {
					$existingMap[$eo['name'] . '_' . $eo['odd_raw']] = true;
				}

				$seenOdds = [];

				foreach ($matchOdds as $oddGroup) {
					foreach ($oddGroup as $oddData) {
						if (!isset($oddData['name'], $oddData['odd'])) continue;

						$name = addslashes($oddData['name']);
						$oddRaw = $oddData['odd'];

						$uniqueKey = "{$matchId}_{$betTypeId}_{$name}_{$oddRaw}";
						if (isset($seenOdds[$uniqueKey])) continue;
						if (isset($existingMap[$name . '_' . $oddRaw])) continue; 

						$seenOdds[$uniqueKey] = true;
						$timestamp = time();

						$sqlValues[] = "(
							{$matchId}, 
							{$betTypeId}, 
							'{$name}', 
							'{$oddRaw}', 
							'{$oddRaw}', 
							{$timestamp}, 
							{$timestamp}
						)";

						$this->customStdout("SQL value to insert:\n" . end($sqlValues) . "\n");
					}
				}
			}
		}

		if (!empty($sqlValues)) {
			$this->customStdout("Total values to insert: " . count($sqlValues) . "\n");

			$sql = "INSERT INTO odd 
					(sport_match_id, odd_bet_type_id, name, odd_raw, odd, created_at, updated_at) 
					VALUES " . implode(", ", $sqlValues);

			\Yii::$app->db->createCommand($sql)->execute();

			$this->customStdout("Removing old duplicate odds...\n");

			$deleteSql = "
				DELETE t1
				FROM odd t1
				JOIN odd t2
				ON t1.name = t2.name
				AND t1.sport_match_id = t2.sport_match_id
				AND t1.created_at < t2.created_at
			";
			\Yii::$app->db->createCommand($deleteSql)->execute();

			$this->customStdout("Old duplicates removed.\n");
		} else {
			$this->customStdout("No odds to insert.\n");
		}
	}






	/**
	 * Generic Method for renew odds for specific sport
	 *
	 * @param array $sport (database record of sport)
	 * @param BaseSport $sportClass (instance of the sport class)
	 */
	public function renewOdds($sport, $sportClass)
	{	
		$sportId = $sport["id"];
		$activeEnabledSeasons = Season::getEnabledActiveSeasonBySportId($sportId);
		$enabledOddBetTypes = OddBetType::getEnabledRecordsBySportId($sportId);
		$sportMatchesIds = SportMatch::getBetMatchesIdBySportIdGroupByVendorId($sportId, true);

		$sportMatchesInternalIds = array_column($sportMatchesIds, "id");

		$matchOddsRecords = Odd::getOddsByMatchIds($sportMatchesInternalIds, true);

		$oddsToUpdate = [];
		$countOfCreatedOdds = 0;

		foreach ($activeEnabledSeasons as $row) {
			$categoryVendorId = $row->category->id_vendor;
			$categoryName = $row->category->name;
			$seasonYear = $row->year;

			foreach ($enabledOddBetTypes as $oddBetType) {
				$oddBetTypeVendorId = $oddBetType["id_vendor"];
				$oddBetTypeId = $oddBetType["id"];
			
				try {
							
					$this->customStdout(sprintf("Sync odds - Category: %s | Season: %s | Bet Type %s", $categoryName, $seasonYear, $oddBetType["name"]));
				//	var_dump("legues " . $categoryVendorId);
				//	var_dump("season ". $seasonYear);
				//	var_dump("bet ". $oddBetTypeVendorId);
				//	var_dump("Season used for API: " . $seasonYear);
				//	exit;
					$matchOdds = $sportClass->getOdds([
						"league" => $categoryVendorId,
						"season" => $seasonYear,
						"bet" => $oddBetTypeVendorId,
					]);
				
					$countMatchOddsToSync = count($matchOdds);
					$this->customStdout(sprintf("Odds to sync: %d", $countMatchOddsToSync));

					if (!empty($matchOdds)) {
						foreach ($matchOdds as $matchOdd) {
							$alreadyUpdatedOddNames = [];
				
							foreach ($matchOdd as $oddRow) {
								if (isset($sportMatchesIds[$oddRow["match_vendor_id"]])) {
									if(in_array($oddRow["name"], $alreadyUpdatedOddNames)) {
										// Skip already updated odd for specific match
										continue;
									}
						
							$alreadyUpdatedOddNames[] = $oddRow["name"];
									$matchId = $sportMatchesIds[$oddRow["match_vendor_id"]]["id"];
									$oddRaw = $oddRow["odd"];
					
							
									$name = $oddRow["name"];

									

									$existingOdds = array_filter($matchOddsRecords, function ($record) use ($name, $matchId, $oddBetTypeId) {
										return (
											$record['name'] === $name
											&& $record['sport_match_id'] === $matchId
											&& $record['odd_bet_type_id'] === $oddBetTypeId
										);
									});
									$existingOdd = reset($existingOdds);
									if (!empty($existingOdd)) {
										$oddsToUpdate[] = [
											"id" => $existingOdd["id"],
											"odd_raw" => $oddRaw,
											"odd" => $oddRaw,
											"updated_at" => time()
										];
									} else {
										$odd = new Odd();
										$odd->name = $name;
										$odd->odd_raw = $oddRaw;
										$odd->odd = $oddRaw;
										$odd->sport_match_id = $matchId;
										$odd->odd_bet_type_id = $oddBetTypeId;

										$result = $odd->validate() && $odd->save();

										if ($result === false) {
											$message = sprintf("Error insert new odd (Category: %s | Season: %d | Bet Type %s | Odd: %s)", $categoryName, $seasonYear, $oddBetType["name"], $name);
											Yii::error([
											"message" => $message,
											"parameters" => $oddRow,
											"errors" => $odd->getErrors()
											], 'data-sync');
											$this->customStderr($message);
										} else {
											$countOfCreatedOdds++;
										}
									}
								}
							}
						}
					}
				} catch (\Exception $e) {
				
					$message = sprintf(
										"Error syncing odds (Category: %s | Season: %d | Bet Type %s | Odd: %s).",
										$categoryName,
										$seasonYear,
										$oddBetType["name"],
										$e->getMessage()
									);
					Yii::error($message, 'data-sync');
					$this->customStderr($message);
				}
			}
		}

		$this->customStdout(sprintf("Odds created: %d", $countOfCreatedOdds));
		$this->customStdout(sprintf("Odds to update: %d", count($oddsToUpdate)));
		if (!empty($oddsToUpdate)) {
			try {
				Odd::batchUpdateRecordsById($oddsToUpdate);
			} catch (\Exception $e) {
				$message = "Error updating odds";
				Yii::error([
					"message" => $message,
					"error" => $e->getMessage(),
				], 'data-sync');
				$this->customStderr($message);
			}
		}
	}

	// ========================================================================================================
	// Sync Odd Bet Types
	// ========================================================================================================


	/**
	 * Syncs the odd bet types for all sports by fetching new data from the API and updating the database.
	 * This method iterates through all sports, retrieves their respective classes, and calls the syncSportOddBetTypes method.
	 *
	 * @return void
	 */
	public function actionSyncOddBetType()
	{
		$this->customStdout("Start syncing odd bet type for all sports \n");

		$sports = Sport::getAllSports();

		$allDataUpdated = true;
		foreach ($sports as $sport) {
			$alias = $sport["alias"];

			try {
				$sportClass = BaseSport::getSportClassByAlias($alias);
				$this->syncSportOddBetTypes($sport, new $sportClass);
			
			} catch (Exception $e) {
				$allDataUpdated = false;
				$message = sprintf("Error getting sport class for alias %s: %s", $alias, $e->getMessage());
				Yii::error($message, 'data-sync');
				$this->customStderr($message . "\n");
				continue;
			}
		}
		if ($allDataUpdated) {
			AppMonitor::updateStatus("SYNC_ODD_BET_TYPE");
		}
		$this->customStdout("Finished syncing odd bet type for all sports \n");
	}

	/**
	 * Updates the odd bet types for a given sport by fetching new data and 
	 * passing it to the synchronization process, which stores the data in the database.
	 *
	 * @param string $sport The name or identifier of the sport.
	 * @param array $oddBetTypes An array of odd bet types to be updated.
	 * @param $sportClass The class or category of the sport.
	 * 
	 * @return void
	 */
	public function syncSportOddBetTypes($sport, $sportClass)
	{
		try {
			$this->customStdout(sprintf("Start - %s", $sport["name"]));

			if (!is_subclass_of($sportClass, BaseSport::class)) {
				throw new \InvalidArgumentException("The provided sportClass must be a subclass of BaseSport.");
			}

			// Load odd bet types from the sport class
			$oddBetTypes = $sportClass->getSportOddBetsTypes();

			// Save the odd bet types to the database
			$this->saveSportOddBetTypes($sport["id"], $oddBetTypes);

			$this->customStdout(sprintf("Finished - %s \n", $sport["name"]));
		} catch (\Exception $e) {
			$message = sprintf("Error syncing odd bet types for sport: %s - %s \n", $sport["name"], $e->getMessage());
			Yii::error($message, 'data-sync');
			$this->customStderr($message . "\n");
		}
	}

	/**
	 * Saves the odd bet types for a specific sport by updating existing records and inserting new ones.
	 *
	 * @param int $sportId The ID of the sport.
	 * @param array $apiOddBetTypesData The data of odd bet types fetched from the API.
	 *
	 * @return void
	 */
	private function saveSportOddBetTypes($sportId, $apiOddBetTypesData)
	{
		$currentRecordsOddBetTypes = OddBetType::getOddBetTypesBySportId($sportId);

		# Define base variables
		$oddBetTypesToUpdate = [];

		#Prepare matches to update based on new data from API Sport
		foreach ($currentRecordsOddBetTypes as $oddBetType) {
			$record = [
				"id" => $oddBetType["id"],
				"id_vendor" => $oddBetType["id_vendor"],
				"name" => $apiOddBetTypesData[$oddBetType["id_vendor"]]["name"] ?? $oddBetType["name"],
				"updated_at" => time(),
			];

			$oddBetTypesToUpdate[] = $record;
			if (isset($apiOddBetTypesData[$oddBetType["id_vendor"]])) {
				unset($apiOddBetTypesData[$oddBetType["id_vendor"]]);
			}
		}

		// Update odd bet types in database
		$this->customStdout(sprintf("Odd bet types to update: %d", count($oddBetTypesToUpdate)));
		if (!empty($oddBetTypesToUpdate)) {
			try {
				OddBetType::batchUpdateRecordsById($oddBetTypesToUpdate);
			} catch (\Exception $e) {
				$message = "Error updating odd bet types";
				Yii::error([
					"message" => $message,
					"error" => $e->getMessage(),
				], 'data-sync');
				$this->customStderr($message);
			}
		}


		// Insert odd bet types in database
		$this->customStdout(sprintf("Odd bet types to insert: %d", count($apiOddBetTypesData)));

		foreach ($apiOddBetTypesData as $row) {
			$oddBetType = new OddBetType();
			$oddBetType->id_vendor = $row["id_vendor"];
			$oddBetType->name = $row["name"];
			$oddBetType->sport_id = $sportId;

			$result = $oddBetType->validate() && $oddBetType->save();

			if ($result === false) {
				Yii::error([
					"message" => "Error insert odd bet type",
					"parameters" => $row,
					"errors" => $oddBetType->getErrors()
				], 'data-sync');
				$this->customStderr(sprintf("Error insert odd bet type for vendor ID: %d", $row["id_vendor"]));
			}
		}
	}

	// ========================================================================================================
	// Sync Matches - From Today to History (For small period of time)
	// ========================================================================================================

	/**
	 * Syncs the latest matches from today to history for all sports.
	 */
	public function actionSyncLatestMatchesFromTodayToHistory()
	{
		$this->customStdout("Start syncing matches for all sports for today" . "\n");
		$sports = Sport::getAllSports();

		$allDataUpdated = true;
		foreach ($sports as $sport) {
			$alias = $sport["alias"];

			try {
				$sportClass = BaseSport::getSportClassByAlias($alias);
				$this->syncLatestMatchesFromTodayToHistory($sport, new $sportClass);
			} catch (Exception $e) {
				$allDataUpdated = false;
				$message = sprintf("Error getting sport class for alias %s: %s", $alias, $e->getMessage());
				Yii::error($message, 'data-sync');
				$this->customStderr($message . "\n");
				continue;
			}
		}
		if($allDataUpdated) {
			AppMonitor::updateStatus("SYNC_LAST_MATCHES");
		}

		$this->customStdout("Finished syncing matches for all sports for today" . "\n");
	}

	/**
	 * Loads matches for specific sport for short period of time (from today to history)
	 *
	 * @param array $sport The sport data.
	 * @param BaseSport $sportClass The sport class instance.
	 */
	public function syncLatestMatchesFromTodayToHistory($sport, $sportClass)
	{

		try {
			$this->customStdout(sprintf("Start - %s", $sport["name"]));

			if (!is_subclass_of($sportClass, BaseSport::class)) {
				throw new \InvalidArgumentException("The provided sportClass must be a subclass of BaseSport.");
			}

			$loadUntilLastDays = 5;
			$matches = $sportClass->getMatchesForDaysFromTodayToHistory($loadUntilLastDays);
	
			$this->updateMatchesAndResults($sport, $matches, $sportClass);


			$this->customStdout(sprintf("Finished - %s \n", $sport["name"]));
		} catch (\Exception $e) {
			$message = sprintf("Error syncing odd bet types for sport: %s - %s \n", $sport["name"], $e->getMessage());
			Yii::error($message, 'data-sync');
			$this->customStderr($message . "\n");
		}
	}

	/**	 
	 * Updates matches and results based on the provided sport, API matches data, and sport class.
	 * This method will update existing matches and evaluate results if necessary.
	 *
	 * @param array $sport (database record of sport)
	 * @param array $apiMatchesData (data from API Sport)
	 * @param BaseSport $sportClass (instance of the sport class)
	 */
	public function updateMatchesAndResults($sport, $apiMatchesData, $sportClass)
	{
		$inProgressStatuses = $sportClass::getInProgressStatuses();

		// Load current matches from database
		$matcheVendorIds = array_column($apiMatchesData, "id_vendor");
		$matches = SportMatch::getSportMatchesBySportIds($sport["id"], $matcheVendorIds);

		# Define base variables
		$updateMatchesCount = 0;
		$evaluatedMatchesCount = 0;

		#Prepare matches to update based on new data from API Sport
		foreach ($apiMatchesData as $match) {
			// Check if record exist in database (based on vendor ID that we got from Sport API)
			$newMatchData = $match;
			$currentMatchData = array_filter($matches, function ($m) use ($match) {
				return $m["id_vendor"] === $match["id_vendor"];
			});
			$currentMatchData = reset($currentMatchData);

			if (!empty($currentMatchData)) {
				// Update basic match data
				$currentMatchData->match_start = $newMatchData["match_start"];
				$currentMatchData->in_progress = (in_array($newMatchData["status"], $inProgressStatuses) ? 1 : 0);
				$currentMatchData->status = $newMatchData["status"];
				$currentMatchData->status_name = $newMatchData["status_name"];
				$currentMatchData->match_start = $newMatchData["match_start"];

				$currentMatchData->detail = SportMatch::clearAndValidateDetail($newMatchData["detail"]);

				$result = $currentMatchData->validate() && $currentMatchData->save();

				if ($result) {
					$updateMatchesCount++;

					// CHECK if match is finished and not evaluated yet
					if (in_array($newMatchData["status"], $sportClass::getFinishStatuses()) && $currentMatchData->evaluated === 0) {
						$totalResults = $sportClass->dataMapper(BaseSport::SPORT_TOTAL_RESULTS, $newMatchData["detail"]);
						$completeResults = $sportClass->dataMapper($sportClass::SPORT_COMPLETE_RESULTS, $newMatchData["detail"]);

						// Validate total results (if not containing nulls instead of numbers)
						if (BaseSport::validateRestructuredTotalMatchResults($totalResults)) {
							try {
								$transaction = \Yii::$app->db->beginTransaction();

								// Add new sport match results 
								$sportMatchResult = new SportMatchResult();
								$sportMatchResult->sport_match_id = $currentMatchData->id;
								$sportMatchResult->result = $totalResults;
								$sportMatchResult->result_vendor = $completeResults;

								if (!($sportMatchResult->validate() && $sportMatchResult->save())) {
									$transaction->rollBack();
								}

								// Set match as evaluated
								$currentMatchData->evaluated = 1;
								if (!($currentMatchData->validate() && $currentMatchData->save())) {
									$transaction->rollBack();
								}

								// Process changes
								$transaction->commit();
								$evaluatedMatchesCount++;
							} catch (\Exception $e) {
								$message = sprintf("Error processing match result (ID: %d, Vendor ID: %d). %s", $currentMatchData->id, $currentMatchData->id_vendor, $e->getMessage());
								Yii::error($message, 'data-sync');
								$this->customStderr($message);
								continue;
							}
						}
					} elseif(in_array($newMatchData["status"], $sportClass::getAbandonedStatuses()) || in_array($newMatchData["status"], $sportClass::getCancelledStatuses()) || in_array($newMatchData["status"],$sportClass::getPostponedStatuses()) && $currentMatchData->evaluated === 0) {
						// Cancelled, Abandoned or Postponed matches - Cancel bets created by user
						try {
							$currentMatchData->cancelUserBets();
							$evaluatedMatchesCount++;
						} catch (\Exception $e) {
							$message = sprintf("Error processing match result (ID: %d, Vendor ID: %d). %s", $currentMatchData->id, $currentMatchData->id_vendor, $e->getMessage());
							Yii::error($message, 'data-sync');
							$this->customStderr($message);
							continue;
						}
					}
				} else {
					$message = sprintf("Error updating match (ID: %d, Vendor ID: %s)", $currentMatchData->id, $currentMatchData->id_vendor);
					Yii::error([
						"message" => $message,
						"parameters" => $newMatchData,
						"errors" => $currentMatchData->getErrors()
					], 'data-sync');
					$this->customStderr($message);
				}
			}
		}
		$this->customStdout(sprintf("Matches updated: %d", $updateMatchesCount));
		$this->customStdout(sprintf("Matches evaluated: %d", $evaluatedMatchesCount));
	}

	// ========================================================================================================
	// Sync Matches
	// ========================================================================================================

	/**
	 * Sync Matches
	 * This method will sync matches for all sports
	 * It will call specific methods for each sport to handle the syncing process
	 */
	public function actionSyncMatches()
	{
		$this->customStdout("Start syncing matches for all sports" . "\n");
		$sports = Sport::getAllSports();
		/*$sports = [
			7 => [
				'id' => 4,
				'name' => 'Basketball',
				'alias' => 'basketball',
				'created_at' => 1750277706,
				'updated_at' => 1750277706,
			],
		];*/
		$allDataUpdated = true;
		foreach ($sports as $sport) {
			$alias = $sport["alias"];

			$categories = Category::getEnablesCategoriesBySportId($sport["id"]);
			try {
				$sportClass = BaseSport::getSportClassByAlias($alias);
				foreach ($categories as $category) {


					$this->syncSportMatches($sport, $category ,new $sportClass);
				}
			} catch (Exception $e) {
				$allDataUpdated = false;
				$message = sprintf("Error getting sport class for alias %s: %s", $alias, $e->getMessage());
				Yii::error($message, 'data-sync');
				$this->customStderr($message . "\n");
				continue;
			}
		}
		if($allDataUpdated){
			AppMonitor::updateStatus("SYNC_MATCHES");
		}
		$this->customStdout("Finished syncing matches for all sports" . "\n");
	}

	/**
	 * Sync Matches for specific sport and category
	 * This method will update existing matches and add new matches based on data from API Sport
	 *
	 * @param array $sport (database record of sport)
	 * @param array $category (database record of category)
	 * @param BaseSport $sportClass (instance of the sport class)
	 */
	public function syncSportMatches($sport, $category, $sportClass)
	{
		try {
			$this->customStdout(sprintf("Start - %s (Category: %s)", $sport["name"], $category["name"]));

			if (!is_subclass_of($sportClass, BaseSport::class)) {
				throw new \InvalidArgumentException("The provided sportClass must be a subclass of BaseSport.");
			}

			$matchesByRangeDates = false;
			if ($sportClass instanceof Football) {
				$matchesByRangeDates = true;
			}
	
			$matches = $sportClass->getUpcomingMatchesForActiveSeasonsByCategoryId($category, $matchesByRangeDates);
			
			$this->syncMatches($sport, $category, $matches, $sportClass);


			$this->customStdout(sprintf("Finished - %s \n", $sport["name"], $category, $sportClass));
		} catch (\Exception $e) {
			$message = sprintf("Error syncing matches for sport: %s - %s \n", $sport["name"], $e->getMessage());
			Yii::error($message, 'data-sync');
			$this->customStderr($message . "\n");
		}
	}

	/**
	 * Sync Matches for specific sport and category
	 * This method will update existing matches and add new matches based on data from API Sport
	 *
	 * @param array $sport (database record of sport)
	 * @param array $category (database record of category)
	 * @param array $apiMatchesData (data from API Sport)
	 */
	private function syncMatches($sport, $category, $apiMatchesData, $sportClass)
	{
		$inProgressStatuses = $sportClass::getInProgressStatuses();
		
		// Load current matches from database
		$seasons = Season::getSeasonsByCategoryId($category["id"]);
		$matches = SportMatch::getMatchesByCategoryId($category["id"], true);

		# Define base variables
		$matchesToUpdate = [];
		#Prepare matches to update based on new data from API Sport
		foreach ($matches as $match) {
			$status =  $apiMatchesData[$match["id_vendor"]]["status"] ?? $match["status"];

			$record = [
				"id" => $match["id"],
				"id_vendor" => $match["id_vendor"],
				"name" => $apiMatchesData[$match["id_vendor"]]["name"] ?? $match["name"],
				"match_start" => $apiMatchesData[$match["id_vendor"]]["match_start"] ?? $match["match_start"],
				"in_progress" => (in_array($status, $inProgressStatuses) ? 1 : 0),
				"extra" => $apiMatchesData[$match["id_vendor"]]["extra"] ?? $match["extra"],
				"status" => $status,
				"status_name" => $apiMatchesData[$match["id_vendor"]]["status_name"] ?? $match["status_name"],
				"home" => $apiMatchesData[$match["id_vendor"]]["home"] ?? $match["home"],
				"away" => $apiMatchesData[$match["id_vendor"]]["away"] ?? $match["away"],
				"detail" => SportMatch::clearAndValidateDetail($apiMatchesData[$match["id_vendor"]]["detail"] ?? json_decode($match["detail"],true)  ?? []),
				"sport_id" => $match["sport_id"],
				"category_id" => $match["category_id"],
				"season_id" => $match["season_id"],
				"updated_at" => time(),
			];


			
			$matchesToUpdate[] = $record;
			if (isset($apiMatchesData[$match["id_vendor"]])) {
				unset($apiMatchesData[$match["id_vendor"]]);
			}
		}
		// Update matches in database for specific category
		$this->customStdout(sprintf("Matches to update: %d", count($matchesToUpdate)));
		if (!empty($matchesToUpdate)) {
			try {
				SportMatch::batchUpdateRecordsById($matchesToUpdate);
			} catch (\Exception $e) {
				$message = "Error updating matches";
				Yii::error([
					"message" => $message,
					"error" => $e->getMessage(),
				], 'data-sync');
				$this->customStderr($message);
			}
		}

		# Add new categories to database
		$this->customStdout(sprintf("Matches to create: %d", count($apiMatchesData)));
		foreach ($apiMatchesData as $vendorId => $row) {
			$sportMatch = new SportMatch();
			$sportMatch->id_vendor = $row["id_vendor"];
			$sportMatch->name = $row["name"];
			$sportMatch->in_progress = (in_array($row["status"], $inProgressStatuses) ? 1 : 0);
			$sportMatch->match_start = $row["match_start"];
			$sportMatch->extra = $row["extra"];
			$sportMatch->status = $row["status"];
			$sportMatch->status_name = $row["status_name"];
			$sportMatch->home = $row["home"];
			$sportMatch->away = $row["away"];
			$sportMatch->detail = SportMatch::clearAndValidateDetail($row["detail"] ?? []);
			
			$sportMatch->sport_id = $sport["id"];
			$sportMatch->category_id = $category["id"];

			$seasonsKey = array_search($row["season_year"], array_column($seasons, 'year'));
			$sportMatch->season_id = $seasons[$seasonsKey]["id"] ?? null;

			$result = $sportMatch->validate() && $sportMatch->save();

			if ($result === false) {
				Yii::error([
					"message" => "Error insert match",
					"parameters" => $row,
					"errors" => $sportMatch->getErrors()
				], 'data-sync');

				$this->customStderr(sprintf("Error insert match for vendor ID: %d", $row["id_vendor"]));
			}
		}
	}

	// ========================================================================================================
	// Sync Categories and Seasons
	// ========================================================================================================

	/*
	 * Sync Categories and Seasons
	 * This method will sync categories and seasons for all sports
	 * It will call specific methods for each sport to handle the syncing process
	 */
	public function actionSyncCategories()
	{
		$this->customStdout("Start syncing categories and seasons for all sports \n");
		$sports = Sport::getAllSports();

		foreach ($sports as $sport) {
			$alias = $sport["alias"];

			// SET RANGE FOR SEASONS (MIN-MAX)
			$activeSeasonsProps = BaseSport::getActiveSportSeasonsProp($sportCategorySeasonConfig['active_max_year_offset'] ?? 1);
			$seasonOffsetMin =  intval(reset($activeSeasonsProps)['season']);
			$seasonOffsetMax = intval(end($activeSeasonsProps)['season']);

			switch ($alias) {
				case 'football':
					$this->syncSportCategoriesAndSeasons($sport, $seasonOffsetMin, $seasonOffsetMax, new Football());
					break;
				case 'baseball':
					$this->syncSportCategoriesAndSeasons($sport, $seasonOffsetMin, $seasonOffsetMax,  new Baseball());
					break;
				case 'basketball':
					$this->syncSportCategoriesAndSeasons($sport, $seasonOffsetMin, $seasonOffsetMax, new Basketball());
					break;
				case 'hockey':
					$this->syncSportCategoriesAndSeasons($sport, $seasonOffsetMin, $seasonOffsetMax, new Hockey());

					break;
				case 'rugby':
					$this->syncSportCategoriesAndSeasons($sport, $seasonOffsetMin, $seasonOffsetMax, new Rugby());

					break;
				case 'nfl':
					$this->syncSportCategoriesAndSeasons($sport, $seasonOffsetMin, $seasonOffsetMax, new Nfl());

					break;
				case 'handball':
					$this->syncSportCategoriesAndSeasons($sport, $seasonOffsetMin, $seasonOffsetMax, new Handball());

					break;
				case 'volleyball':
					$this->syncSportCategoriesAndSeasons($sport, $seasonOffsetMin, $seasonOffsetMax, new Volleyball());

					break;
				default:
			}
		}
		AppMonitor::updateStatus("SYNC_CATEGORIES");

		$this->customStdout("Finished syncing categories and seasons for all sports");
	}

	/**
	 * Sync Categories and Seasons for specific sport
	 * This method will update existing categories and seasons and add new categories and seasons based on data from API Sport
	 *
	 * @param array $sport (database record of sport)
	 * @param int $seasonOffsetMin (minimum year offset for seasons)
	 * @param int $seasonOffsetMax (maximum year offset for seasons)
	 * @param BaseSport $sportClass (instance of the sport class)
	 */
	public function syncSportCategoriesAndSeasons($sport, $seasonOffsetMin, $seasonOffsetMax, $sportClass)
	{
		try {
			$this->customStdout(sprintf("Start - %s", $sport["name"]));

			if (!is_subclass_of($sportClass, BaseSport::class)) {
				throw new \InvalidArgumentException("The provided sportClass must be a subclass of BaseSport.");
			}

			$categories = $sportClass->getCategories();

			$this->syncCategoriesAndSeasons($sport, $categories, $seasonOffsetMin, $seasonOffsetMax);

			$this->customStdout(sprintf("Finished - %s \n", $sport["name"]));
		} catch (\Exception $e) {
			$message = sprintf("Error syncing categories & seasons for sport: %s - %s \n", $sport["name"], $e->getMessage());
			Yii::error($message, 'data-sync');
			$this->customStderr($message . "\n");
		}
	}


	/*	 
	 * This method will update existing categories and seasons and add new categories and seasons based on data from API Sport
	 *
	 * @param array $sport (database record of sport)
	 * @param array $apiCategoriesData (data from API Sport)
	 * @param int $seasonOffsetMin (minimum year offset for seasons)
	 * @param int $seasonOffsetMax (maximum year offset for seasons)
	 */
	private function syncCategoriesAndSeasons($sport, $apiCategoriesData, $seasonOffsetMin, $seasonOffsetMax)
	{
		$categories = Category::getCategoriesBySportId($sport["id"]);
		$seasons = Season::getSeasonsBySportId($sport["id"]);

		$categoriesToUpdate = [];
		$seasonsToUpdate = [];
		$seasonsToInsert = [];
		$currentActiveSeasons = [];

		$currentYear = (int)date('Y');
		$maxYearsBack = 1; // maximálně 1 rok zpět

		// --- Update existujících kategorií ---
		foreach ($categories as $category) {
			$record = [
				"id" => $category["id"],
				"name" => $apiCategoriesData[$category["id_vendor"]]["name"] ?? $category["name"],
				"country_name" => $apiCategoriesData[$category["id_vendor"]]["country_name"] ?? $category["country_name"],
				"logo_url" => $apiCategoriesData[$category["id_vendor"]]["logo_url"] ?? $category["logo_url"],
				"updated_at" => time(),
				"active_session" => 0,
			];

			if (isset($apiCategoriesData[$category["id_vendor"]]["seasons"])) {
				$latestSeason = null;
				$latestYear = 0;

				foreach ($apiCategoriesData[$category["id_vendor"]]["seasons"] as $season) {
					$seasonYear = 0;

					// dvouletá sezóna (např. 2024-2025)
					if (preg_match('/^\d{4}-(\d{4})$/', $season['year'], $matches)) {
						$seasonYear = (int)$matches[1];
					} elseif (preg_match('/^\d{4}$/', $season['year'])) {
						$seasonYear = (int)$season['year'];
					} else {
						continue;
					}

					// validace maximálně 1 rok zpět
					if ($seasonYear < $currentYear - $maxYearsBack) {
						continue;
					}

					// vyber nejnovější sezónu
					if ($latestSeason === null || $seasonYear > $latestYear) {
						$latestSeason = $season;
						$latestYear = $seasonYear;
					}
				}

				if ($latestSeason !== null) {
					$record["active_session"] = 1;
					$latestSeason["category_id"] = $category["id"];
					$latestSeason["active"] = 1;
					$latestSeason["category_enabled"] = $category["enabled"];
					$currentActiveSeasons[] = $latestSeason;
				}
			}

			$categoriesToUpdate[] = $record;
			unset($apiCategoriesData[$category["id_vendor"]]);
		}

		// Update existující kategorie
		if (!empty($categoriesToUpdate)) {
			Category::batchUpdateRecordsById($categoriesToUpdate);
		}

		// --- Insert nové kategorie ---
		foreach ($apiCategoriesData as $vendorId => $row) {
			$category = new Category();
			$category->id_vendor = $row["id_vendor"];
			$category->name = $row["name"];
			$category->country_name = $row["country_name"];
			$category->logo_url = $row["logo_url"];
			$category->active_session = 1;
			$category->sport_id = $sport["id"];

			if ($category->validate() && $category->save()) {
				$latestSeason = null;
				$latestYear = 0;

				foreach ($row["seasons"] as $season) {
					$seasonYear = 0;

					if (preg_match('/^\d{4}-(\d{4})$/', $season['year'], $matches)) {
						$seasonYear = (int)$matches[1];
					} elseif (preg_match('/^\d{4}$/', $season['year'])) {
						$seasonYear = (int)$season['year'];
					} else {
						continue;
					}

					if ($seasonYear < $currentYear - $maxYearsBack) {
						continue;
					}

					if ($latestSeason === null || $seasonYear > $latestYear) {
						$latestSeason = $season;
						$latestYear = $seasonYear;
					}
				}

				if ($latestSeason !== null) {
					$latestSeason["category_id"] = $category->id;
					$latestSeason["active"] = 1;
					$latestSeason["category_enabled"] = 0;
					$currentActiveSeasons[] = $latestSeason;
				}
			}
		}

		// --- Update existujících sezón ---
		foreach ($seasons as $season) {
			$currentSeasonData = null;

			foreach ($currentActiveSeasons as $key => $item) {
				if ($item['category_id'] === $season['category_id']) {
					$currentSeasonData = $item;
					unset($currentActiveSeasons[$key]);
					break;
				}
			}

			$seasonsToUpdate[] = [
				"id" => $season["id"],
				"current" => $currentSeasonData["current"] ?? $season["current"],
				"odds" => $currentSeasonData["odds"] ?? $season["odds"],
				"category_enabled" => $currentSeasonData["category_enabled"] ?? $season["category_enabled"],
				"active" => $currentSeasonData["active"] ?? $season["active"],
				"updated_at" => time(),
			];
		}

		// --- Insert nové sezóny ---
		foreach ($currentActiveSeasons as $season) {
			$seasonsToInsert[] = [
				"id" => null,
				"year" => $season["year"],
				"current" => $season["current"] ?? 0,
				"odds" => $season["odds"] ?? 0,
				"active" => 1,
				"category_enabled" => $season["category_enabled"],
				"category_id" => $season["category_id"],
				"sport_id" => $sport["id"],
				"created_at" => time(),
				"updated_at" => time(),
			];
		}

		if (!empty($seasonsToUpdate)) {
			Season::batchUpdateRecordsById($seasonsToUpdate);
		}

		if (!empty($seasonsToInsert)) {
			Season::insertSeasons($seasonsToInsert);
		}
	}


	// ========================================================================================================

	/**
	 * Check if season meets the offsets
	 * This method will check if the season year is within the specified range
	 *
	 * @param int $year The year of the season
	 * @param int $minYear The minimum year offset
	 * @param int $maxYear The maximum year offset
	 * @return bool True if the season meets the offsets, false otherwise
	 */
	public static function seasonMeetsOffsets($year, $minYear, $maxYear)
	{
		if ($year >= $minYear && $year <= $maxYear) {
			return true;
		}
		return false;
	}

	// ========================================================================================================
	private function customStdout($message)
	{
		$this->stdout(date('Y-m-d H:i:s') . "\t" . $message . "\n");
	}

	private function customStderr($message)
	{
		$this->stderr(date('Y-m-d H:i:s') . "\t" . $message . "\n", \yii\helpers\Console::FG_RED);
	}
}
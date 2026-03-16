<?php

namespace console\controllers;

use common\models\AppMonitor;
use common\models\OddBetType;
use yii\console\Controller;
use Yii;

class OddBetTypeController extends Controller
{

	/**
	 * Enable and configure default odd bet types based on the configuration in params.php.
	 * 
	 * Run ./yii odd-bet-type/enable-and-configure-default-odd-bet-types
	 */
	public function actionEnableAndConfigureDefaultOddBetTypes()
	{
		$oddBetTypes = OddBetType::find()->all();

		$this->stdout("Enabling set aliases for odd bet type: " . count($oddBetTypes) . "\n");
	
		foreach ($oddBetTypes as $oddBetType) {
			$enabledOddBetTypes = Yii::$app->params['enabledOddBetTypes'] ?? [];
			$configurationForOddBetType = null;
			
			foreach($enabledOddBetTypes as $index => $row) {			
				if (in_array($oddBetType->name, $row["names"])) {
					$configurationForOddBetType = $row;
					break;
				}
			}

			if($configurationForOddBetType === null){
				$oddBetType->setAsDisabled();
			} else {
				$oddBetType->setAsEnabled($configurationForOddBetType["alias"] ?? null, $configurationForOddBetType["rank"] ?? 0);
			}

			if ($oddBetType->save()) {
				$this->stdout("ID: {$oddBetType->id}, Enabled: {$oddBetType->enabled}, Alias: {$oddBetType->alias}\n");
			}
		}

		AppMonitor::updateStatus("ENABLE_AND_CONFIGURE_ODD_BET_TYPES");
		$this->stdout("Finished enabling and configuring odd bet types.\n");

		try {
			$affected = OddBetType::updateAll(
				[
					"alias"      => "match-winner",
					"rank"       => 10,
					"enabled"    => 1,
					"updated_at" => time(),
				],
				[
					"name" => "3Way Result",
					"sport_id" => [2, 6, 8], 
				]
			);

			if ($affected > 0) {
				$this->stdout("Special odd bet type updated (3Way Result, sport_id in 2,6,8).\n");
			} else {
				$this->stderr("Special odd bet type not found (3Way Result, sport_id in 2,6,8).\n");
			}
		} catch (\Exception $e) {
			Yii::error([
				"message" => "Error updating special odd bet type (3Way Result, sport_id in 2,6,8)",
				"error"   => $e->getMessage(),
			], 'data-sync');
			$this->customStderr("Error updating special odd bet type.");
		}
	}
}

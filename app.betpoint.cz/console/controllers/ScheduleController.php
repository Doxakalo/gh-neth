<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;


class ScheduleController extends Controller
{
	
	/**
	 * Hourly scheduled task
	 * 
	 * Run ./yii schedule/hourly
	 * 
	 */
	public function actionHourly() {
		$msg = "Hourly scheduled task executed: " . date('Y-m-d H:i:s') . "\n";
		$this->stdout($msg);
		Yii::info($msg, 'schedule');
	}


	/**
	 * Quarter hour scheduled task
	 * 
	 * Run ./yii schedule/quarter-hour
	 * 
	 */
	public function actionQuarterHour() {
		$msg = "Quarter hour scheduled task executed: " . date('Y-m-d H:i:s') . "\n";
		$this->stdout($msg);
		Yii::info($msg, 'schedule');
	}


	/**
	 * Minute scheduled task
	 * 
	 * Run ./yii schedule/minute
	 * 
	 */
	public function actionMinute() {
		$msg = "Minute scheduled task executed: " . date('Y-m-d H:i:s') . "\n";
		$this->stdout($msg);
		Yii::info($msg, 'schedule');
	}
	
}

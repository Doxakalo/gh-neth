<?php
require_once __DIR__ . '/../common/services/CronHandler.php';
date_default_timezone_set('Europe/Prague');

use common\services\CronHandler;


    $today = date('Y-m-d');

    $baseDir = __DIR__;
    $logDir = $baseDir . '/logs/basketball';
    $basketballLogs = "{$logDir}/{$today}_sync_basketball_odds.log";

    $yiiPath = realpath(__DIR__ . '/../yii'); 
    chdir(dirname($yiiPath));

    $cronHandler = new CronHandler();

    $cronHandler->runCommand('api-sync/sync-single-odds basketball "Basketball"', $basketballLogs, $yiiPath);


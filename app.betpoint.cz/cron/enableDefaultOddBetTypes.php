<?php
require_once __DIR__ . '/../common/services/CronHandler.php';
date_default_timezone_set('Europe/Prague');

use common\services\CronHandler;


    $today = date('Y-m-d');

    $baseDir = __DIR__;
    $logDir = $baseDir . '/logs';
    $categoriesLogs = "{$logDir}/{$today}_bet_type.log";

    $yiiPath = realpath(__DIR__ . '/../yii'); 
    chdir(dirname($yiiPath));

    $cronHandler = new CronHandler();

    $cronHandler->runCommand('odd-bet-type/enable-and-configure-default-odd-bet-types', $categoriesLogs, $yiiPath);



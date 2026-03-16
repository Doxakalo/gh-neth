<?php
date_default_timezone_set('Europe/Prague'); 

$logDir = __DIR__ . '/logs';
$logFile = $logDir . '/cron_test.log';

if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

$timestamp = date('Y-m-d H:i:s');
$logMessage = "[$timestamp] Cron běží na serveru – test OK\n";

file_put_contents($logFile, $logMessage, FILE_APPEND);


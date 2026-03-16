<?php

namespace common\services;

class CronHandler
{
    function runCommand($cmd, $logFile, $yiiPath) {
        $fullCmd = escapeshellcmd($yiiPath) . " {$cmd} >> {$logFile} 2>&1";
            echo "Running: {$fullCmd}\n";
            system($fullCmd, $returnVar);
        return $returnVar === 0;
    }

    public function runFile(string $fileName): bool
    {
        $fullCmd = "php {$fileName}";
        echo "Running: {$fullCmd}\n";
        system($fullCmd, $returnVar);

        return $returnVar === 0;
    }

    /**
     * Once per week JSON
     */
    public function runOncePerWeek(int $currentDayOfWeek, string $fileName, string $logDir = '/app/console/runtime/logs/cron'): bool
    {
        if ($currentDayOfWeek !== 3) {
            echo "Today is not Wednesday. Skipping {$fileName}.\n";
            return false;
        }

        $jsonFile = rtrim($logDir, '/').'/weekly_run.json';

        $lastRun = [];
        if (file_exists($jsonFile)) {
            $lastRun = json_decode(file_get_contents($jsonFile), true) ?: [];
        }

        $lastDate = $lastRun[$fileName] ?? null;
        $now = time();

        if ($lastDate && ($now - strtotime($lastDate)) < 7*24*60*60) {
            echo "{$fileName} was already run within the last 7 days (last run: {$lastDate}). Skipping.\n";
            return false;
        }

        $success = $this->runFile($fileName);

        if ($success) {
            $lastRun[$fileName] = date('Y-m-d');
            file_put_contents($jsonFile, json_encode($lastRun, JSON_PRETTY_PRINT));
            echo "{$fileName} executed and last run updated.\n";
        }

        return $success;
    }

    function checkAndLockRun(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            file_put_contents($filePath, json_encode(['run' => false], JSON_PRETTY_PRINT));
        }

        $json = json_decode(file_get_contents($filePath), true);

        if ($json['run'] ?? false) {
            echo "Cron skipped because run.json = true\n";
            return false;
        } else {
            $json['run'] = true;
            file_put_contents($filePath, json_encode($json, JSON_PRETTY_PRINT));
            return true;
        }
    }


    function unlockRun(string $filePath)
    {
        if (file_exists($filePath)) {
            $json = json_decode(file_get_contents($filePath), true);
            $json['run'] = false;
            file_put_contents($filePath, json_encode($json, JSON_PRETTY_PRINT));
        }
    }

    
}
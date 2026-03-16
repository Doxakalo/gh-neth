<?php
/**
 * Time-based Cron Dispatcher
 * - Forced execution support
 * - Priority: full/mid/small > minuteSync
 * - Max 15 min running step
 * - Executes only one step per invocation
 */

require_once __DIR__ . '/../common/services/CronHandler.php';
use common\services\CronHandler;

date_default_timezone_set('Europe/Prague');

$statusFile = __DIR__ . '/logs/cron_status.json';
$logFile = __DIR__ . '/logs/dispatcher.log';


$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
    chmod($logDir, 0777); 
}

$cronHandler = new CronHandler();

// --- Helpers ---
function logMsg($msg) {
    global $logFile;
    file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] $msg\n", FILE_APPEND);
}

function readStatus($file) {
    if (!file_exists($file)) return null;
    return json_decode(file_get_contents($file), true);
}

function writeStatus($file, $data) {
    $tmp = $file . '.tmp';
    file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    rename($tmp, $file);
}

function cleanupHistory(&$status, $maxAgeSeconds = 900) { 
    $now = time();
    if (!empty($status['history']) && is_array($status['history'])) {
        $status['history'] = array_values(array_filter(
            $status['history'],
            fn($entry) => isset($entry['at']) && ($now - $entry['at']) <= $maxAgeSeconds
        ));
    }
}

function ensureStatus($file) {
    if (!file_exists($file)) {
        writeStatus($file, [
            'isRunning' => false,
            'dispatched' => null,
            'steps' => [],
            'current_step' => null,
            'step_started_at' => null,
            'started_at' => null,
            'updated_at' => time(),
            'history' => [],
            'forced' => false,
            'forcedState' => ''
        ]);
    }
}

// --- Case selection based on time ---
function pickCaseByTime($hour, $minute) {
    $midnightSwitcher = [1];
    $midSyncSwitcher = [0,3,6,9,12,15,18,21];
    $hourSyncSwitcher = [2,4,5,7,8,10,11,13,14,16,17,19,20,22,23];

    if ($minute > 2) return 'minuteSync';
    if (in_array($hour, $midnightSwitcher, true)) return 'fullSync';
    if (in_array($hour, $midSyncSwitcher, true)) return 'midSync';
    if (in_array($hour, $hourSyncSwitcher, true)) return 'smallSync';
    return null;
}

// --- Steps definition ---
function getStepsForCase($case) {
    switch ($case) {
        case 'fullSync':
            return [
                'clearDatabase','syncCategories','enableCategories','syncOddBetType',
                'enableDefaultOddBetTypes','syncMatches','syncBaseballOdds','syncBasketballOdds',
                'syncFootballOdds','syncRugbyOdds','syncHockeyOdds','syncNflOdds','syncHandballOdds',
                'syncLatestMatchesFromToday','syncBetsEvaluate'
            ];
        case 'midSync':
            return [
                'syncCategories','enableCategories','syncOddBetType','enableDefaultOddBetTypes',
                'syncMatches','syncBaseballOdds','syncBasketballOdds','syncFootballOdds',
                'syncHockeyOdds','syncNflOdds','syncHandballOdds','syncRugbyOdds', 'clearFrequentedLogs'
            ];
        case 'smallSync':
            return ['syncLatestMatchesFromToday','syncBetsEvaluate'];
        case 'minuteSync':
            return ['syncLatestMatchesFromToday', 'syncBetsEvaluate'];
        default:
            return [];
    }
}

// --- Step starter ---
function runStep($step) {
    //logMsg("Spouštím krok: $step");
    $scriptPath = __DIR__ . "/$step.php";

    if (!file_exists($scriptPath)) {
        //logMsg("Chyba: Skript $scriptPath nenalezen");
        return false;
    }

    $output = [];
    $exitCode = 0;
    exec("php " . escapeshellarg($scriptPath) . " 2>&1", $output, $exitCode);

    foreach ($output as $line) {
        //logMsg("[{$step}] $line");
    }

    if ($exitCode === 0) {
        //logMsg("Dokončen krok: $step (exit code 0)");
        return true;
    } else {
        //logMsg("Selhal krok: $step (exit code $exitCode)");
        return false;
    }
}

// --- main logic ---
$currentHour = (int)date('G');
$currentMinute = (int)date('i');

ensureStatus($statusFile);
$status = readStatus($statusFile);

// --- Time checker is set up for 15 minutes ( in case the active would fail since their limit is 10 minuts ) ---
if ($status['isRunning'] && $status['step_started_at']) {
    $runningTime = time() - $status['step_started_at'];
    if ($runningTime > 900) { // 15 minutes
        //logMsg("Chyba: krok '{$status['current_step']}' běží déle než 15 minut → označuji jako failed");
        $status['steps'][$status['current_step']] = false;
        $status['isRunning'] = false;
        $status['current_step'] = null;
        $status['step_started_at'] = null;
        $status['history'][] = [
            'action' => 'step_failed_timeout',
            'name' => $status['current_step'],
            'at' => time()
        ];
        writeStatus($statusFile, $status);
        exit;
    } else {
        //logMsg("Dispatcher: krok '{$status['current_step']}' stále běží, čekám.");
        exit;
    }
}

// --- forcer ---
if (!empty($status['forced']) && $status['forced'] && $status['forcedState'] !== '') {
    $case = $status['forcedState'];
} else {
    $case = pickCaseByTime($currentHour, $currentMinute);
    // Skiping in case there is forced state
    if ($case === 'minuteSync' && !empty($status['forced']) && $status['forced']) {
        $case = $status['forcedState'];
    }
}

if (!$case) {
    //logMsg("Dispatcher: žádný case pro tento čas ($currentHour:$currentMinute)");
    exit;
}

$steps = getStepsForCase($case);
if (empty($steps)) {
    //logMsg("Dispatcher: case '$case' nemá definované kroky");
    exit;
}

// --- New case initialisation if there isnt running case ---
if (!$status['isRunning']) {
    $status['isRunning'] = true;
    $status['dispatched'] = $case;
    $status['steps'] = array_fill_keys($steps, false);
    $status['current_step'] = null;
    $status['step_started_at'] = null;
    $status['started_at'] = time();
    $status['updated_at'] = time();
    $status['history'][] = [
        'action' => 'dispatched',
        'name' => $case,
        'at' => time()
    ];
    writeStatus($statusFile, $status);
    //logMsg("Dispatcher: nový case '$case' inicializován s kroky: ".implode(',', $steps));
}

// --- Single step starter ---
$pendingSteps = array_keys(array_filter($status['steps'], fn($done) => !$done));
if (!empty($pendingSteps)) {
    $currentStep = $pendingSteps[0];
    $status['current_step'] = $currentStep;
    $status['step_started_at'] = time();
    writeStatus($statusFile, $status);

    $result = runStep($currentStep);

    $status['steps'][$currentStep] = $result ? true : false;
    $status['current_step'] = null;
    $status['step_started_at'] = null;
    $status['updated_at'] = time();
    $status['history'][] = [
        'action' => $result ? 'step_completed' : 'step_failed',
        'name' => $currentStep,
        'at' => time()
    ];

    // --- Reseter if everything were finished + nulling the forcer if there was any ---
    if (!in_array(false, $status['steps'], true)) {
        //logMsg("Dispatcher: všechny kroky dokončeny, stav FREE");
        $status['isRunning'] = false;
        $status['dispatched'] = null;
        $status['steps'] = [];
        if (!empty($status['forced']) && $status['forced']) {
            $status['forced'] = false;
            $status['forcedState'] = '';
            //logMsg("Dispatcher: forced case dokončen, reset forced flag");
        }
    }

    cleanupHistory($status);
    writeStatus($statusFile, $status);
}

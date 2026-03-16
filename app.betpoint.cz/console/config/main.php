<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => \yii\console\controllers\FixtureController::class,
            'namespace' => 'common\fixtures',
          ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning', 'info'],
                ],
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['schedule'],
                    'logVars' => [], // Prevents logging of $_GET, $_POST, $_SESSION, etc.
                    'logFile' => '@runtime/logs/schedule.log',
                ],
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['apiSports'],
                    'logVars' => [], // Prevents logging of $_GET, $_POST, $_SESSION, etc.
                    'logFile' => '@runtime/logs/api-sport.log',
                ],
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'categories' => ['data-sync'],
                    'logVars' => [], // Prevents logging of $_GET, $_POST, $_SESSION, etc.
                    'logFile' => '@runtime/logs/data-sync.log',
                ],
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning', "info"],
                    'categories' => ['bets-evaluate'],
                    'logVars' => [], // Prevents logging of $_GET, $_POST, $_SESSION, etc.
                    'logFile' => '@runtime/logs/bets-evaluate.log',
                ],
            ],
        ],
    ],
    'params' => $params,
];

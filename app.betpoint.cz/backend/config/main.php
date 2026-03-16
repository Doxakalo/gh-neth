<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'sbc-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-sbc-admin',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
				'name' => '_identity-sbc-admin', 
				'httpOnly' => true,
				'secure' => true,
			],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'sbc-admin',
			'cookieParams' => [
				'httpOnly' => true,
				'secure' => true,
			],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'defaultRoute' => 'user/index',
    'container' => [
        'definitions' => [
            \yii\widgets\LinkPager::class => \yii\bootstrap5\LinkPager::class,
        ],
    ],
    'params' => $params,
];

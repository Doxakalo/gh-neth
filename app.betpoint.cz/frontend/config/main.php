<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'sbc-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-sbc',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
				'name' => '_identity-sbc', 
				'httpOnly' => true,
				'secure' => true,
			],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'sbc',
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
				/* Site */
				'' => 'site/index',
				'/<path:[\w-]+>' => 'site/index',
                '/sign-up/<token:[a-z0-9\-]{2,40}>' => 'site/signup-check',
				
				/* API */
				[
					'class' => 'yii\rest\UrlRule',
					'controller' => 'api',
					'pluralize' => false,
					'extraPatterns' => [
						'GET app-status' => 'app-status',
						'POST signup' => 'signup',
						'POST login' => 'login',
						'POST logout' => 'logout',
						'GET account' => 'account',
						'GET sports' => 'sports',
                        'GET sport-matches' => 'sport-matches',
						'POST contact-form' => 'contact-form',
						'POST bet' => 'create-bet',
						'GET bets' => 'bets',
						'GET transactions' => 'transactions',
					],
				],
            ],
        ],
    ],
    'params' => $params,
];

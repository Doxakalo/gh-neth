<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
		'assetManager' => [
			'appendTimestamp' => true,
		],
        'user' => [
            'identityCookie' => [
				'secure' => false,
			],
        ],
        'session' => [
			'cookieParams' => [
				'secure' => false,
			],
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
        /*'allowedIPs' => ['*'],*/
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        'allowedIPs' => ['*'],
    ];
}

return $config;

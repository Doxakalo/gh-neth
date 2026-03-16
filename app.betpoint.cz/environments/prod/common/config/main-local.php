<?php

return [
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=localhost;dbname=app.betpoint.cz',
            'username' => 'app.betpoint.cz',
            'password' => '',
            'charset' => 'utf8mb4',

			// schema caching
			'enableSchemaCache' => true,
			'schemaCacheDuration' => 3600,
			'schemaCache' => 'cache',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
			'transport' => [
				'scheme' => 'smtp',
				'host' => '',
				'username' => '',
				'password' => '',
				'port' => 25,
			 ],
        ],
		'urlManagerBackend' => [
			'hostInfo' => 'https://admin.app.betpoint.cz',
		],
    ],
];

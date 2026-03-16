<?php

return [
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=db.sports-betting-college.docker;dbname=sports_betting_college',
            'username' => 'sports_betting_college',
            'password' => 'sbc000',
            'charset' => 'utf8mb4',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
			'useFileTransport' => false,
			// mailcatcher
			'transport' => [
				'scheme' => 'smtp',
				'host' => 'mailcatcher',
				'username' => '',
				'password' => '',
				'port' => 1025,
			 ],
        ],
		'urlManagerBackend' => [
			'hostInfo' => 'http://admin.sports-betting-college.docker',
		],
    ],
];

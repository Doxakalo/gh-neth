<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
		'authManager' => [
			'class' => 'yii\rbac\DbManager',
		],
		'i18n' => [
			'translations' => [
				'common*' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@common/messages', // Use common messages
					'fileMap' => [
						'common' => 'common.php',
					],
				],
			],
		],
		'formatter' => [
			'class' => 'common\utils\formatters\SbcFormatter',
			'defaultTimeZone' => 'Europe/Prague',
			'currencyCode' => 'DRC',
			'datetimeFormat' => 'php:d/m/Y, H:i', // en-GB 24-hour format
			'dateFormat' => 'php:d/m/Y', // en-GB format
			'timeFormat' => 'php:H:i', // 24-hour format
			'numberFormatterOptions' => [
				NumberFormatter::MIN_FRACTION_DIGITS => 2,
				NumberFormatter::MAX_FRACTION_DIGITS => 2,
			],
		],
		'urlManagerBackend' => [
			'class' => 'yii\web\UrlManager',
			'enablePrettyUrl' => false,
			'showScriptName' => false,
		],
    ],

    'sourceLanguage' => 'en-US', // disable to enable messages translated from en > en
	'language' => 'en-GB',
    'timeZone' => 'Europe/Prague',
    'name'=>'BetPoint',

];

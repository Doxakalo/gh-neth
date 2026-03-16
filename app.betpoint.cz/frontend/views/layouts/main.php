<?php

/** @var \yii\web\View $this */
/** @var string $content */

use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;


$metadata = [
	'title' => Yii::$app->name,
	'description' => Yii::$app->name,
	'keywords' => 'sport, bet, education',
	'author' => Yii::$app->name,
	'url' => Url::to('@web', true),
];


AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<base href="<?= Url::to('@web/') ?>" />
    <meta charset="<?= Yii::$app->charset ?>">
	<meta name="viewport" content="width=device-width,initial-scale=1">

    <title><?= Html::encode($metadata['title']) ?></title>
	<meta name="description" content="<?= Html::encode($metadata['description']) ?>" />
	<meta name="keywords" content="<?= Html::encode($metadata['keywords']) ?>">
	<meta name="author" content="<?= Html::encode($metadata['author']) ?>">

	<meta property="og:title" content="<?= Html::encode($metadata['title']) ?>" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="<?= Html::encode($metadata['url']) ?>" />
	<meta property="og:description" content="<?= Html::encode($metadata['description']) ?>" />

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;700&family=Roboto:wght@500&display=swap" rel="stylesheet">

	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?= Url::to('@web/images/favicon/apple-touch-icon-72x72.png') ?>" />
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?= Url::to('@web/images/favicon/apple-touch-icon-144x144.png') ?>" />
	<link rel="icon" type="image/png" href="<?= Url::to('@web/images/favicon/favicon-32x32.png') ?>" sizes="32x32" />
	<link rel="icon" type="image/png" href="<?= Url::to('@web/images/favicon/favicon-16x16.png') ?>" sizes="16x16" />

    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();

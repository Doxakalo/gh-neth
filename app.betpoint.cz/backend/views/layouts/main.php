<?php

/** @var \yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100"
    data-controller-action="<?= sprintf('%s/%s', Yii::$app->controller->id, Yii::$app->controller->action->id) ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode(sprintf('%s | %s Admin', $this->title, Yii::$app->name)) ?></title>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;700&family=Roboto:wght@500&display=swap" rel="stylesheet">

	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?= Url::to('@web/images/favicon/apple-touch-icon-72x72.png') ?>" />
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?= Url::to('@web/images/favicon/apple-touch-icon-144x144.png') ?>" />
	<link rel="icon" type="image/png" href="<?= Url::to('@web/images/favicon/favicon-32x32.png') ?>" sizes="32x32" />
	<link rel="icon" type="image/png" href="<?= Url::to('@web/images/favicon/favicon-16x16.png') ?>" sizes="16x16" />

	<script type="text/javascript">
		window.appConfig = {
			api: {
				appStatus: '<?= Url::to(['site/app-status']) ?>'
			},
            config: {
               appStatusCheckInterval: 60000
            }
		};
	</script>

    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <div class="app-status-bar" id="app-status-bar">
        <p></p>
    </div>

    <?php if (!Yii::$app->user->isGuest): ?>
    <header>
        <div class="container">
            <div class="header-inner">
                <a class="brand" href="<?= Yii::$app->homeUrl ?>">
                    <img src="<?= Url::to('@web/images/betpoint-logo.svg') ?>" alt="<?= Yii::$app->name ?>">
                    <strong>Admin Panel</strong>
                </a>
                <nav>
                    <?php
                    if (!Yii::$app->user->isGuest) {
                        $menuItems = [];


                         $menuItems[] = [
                            'label' => Yii::t('app', 'categories_title'),
                            'url' => ['categories/index'],
                            'active' => in_array(\Yii::$app->controller->id, ['categories']),
                        ];

                        $menuItems[] = [
                            'label' => Yii::t('app', 'user_list_title'),
                            'url' => ['user/index'],
                            'active' => in_array(\Yii::$app->controller->id, ['user']),
                        ];
                        $menuItems[] = [
                            'label' => Yii::t('app', 'match_list_title'),
                            'url' => ['sport-match/index'],
                            'active' => in_array(\Yii::$app->controller->id, ['sport-match']),
                        ];

                        echo Nav::widget([
                            'options' => ['class' => null],
                            'items' => $menuItems,
                        ]);
                        echo Html::beginForm(['site/logout'], 'post')
                            . Html::submitButton(
                                'Sign Out <i class="icon sbc-icon-logout"></i>',
                                ['class' => null]
                            )
                            . Html::endForm();
                    }
                    ?>
                </nav>
            </div>
        </div>
    </header>
    <?php endif; ?>

    <main role="main" class="flex-grow-1">
        <div class="container h-100">
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer class="footer mt-auto">
        <p>&copy; <?= date('Y') ?> <?= Yii::$app->name ?></p>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();

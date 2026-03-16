<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */

// disable rendering of debug toolbar in mail layout
if (class_exists('yii\debug\Module')) {
	$this->off(\yii\web\View::EVENT_END_BODY, [\yii\debug\Module::getInstance(), 'renderToolbar']);
}

$webUrl = Yii::$app->urlManager->createAbsoluteUrl(['site/index']);
$settingsUrl = Yii::$app->urlManager->createAbsoluteUrl(['site/index']) . 'moje-nastaveni';

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="only">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<table class="wrapper">
	<tr>
		<td align="center">
			<table class="main">
				<tr>
					<td>
						<table class="spacer spacer-30"><tr><td>&nbsp;</td></tr></table>
						
						<table class="content">
							<tr>
								<td class="side-padding">&nbsp;</td>
								<td>
									<?= $content ?>
								</td>
								<td class="side-padding">&nbsp;</td>
							</tr>
						</table>
						
						<table class="spacer spacer-50"><tr><td>&nbsp;</td></tr></table>
					</td>
				</tr>
				
				<tr>
					<td>
						<table class="footer">
							<tr>
								<td class="side-padding">&nbsp;</td>
								<td>
									<table class="spacer spacer-20"><tr><td>&nbsp;</td></tr></table>
									<p>
										&copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?>
									</p>
									<table class="spacer spacer-30"><tr><td>&nbsp;</td></tr></table>
								</td>
								<td class="side-padding">&nbsp;</td>
							</tr>
						</table>						
					</td>
				</tr>

			</table>
		</td>
	</tr>
</table>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\UserBet $userBet */
/** @var string $message */

?>

<?php $this->beginContent('@common/mail/layouts/html.php'); ?>

<p>Hello,</p>

<p>
	Following is a new Complaint from <?= Html::encode(Yii::$app->name) ?> website:
</p>

<p>
	<strong>User:</strong><br>
	<?= Html::a(
			Html::encode($userBet->user->first_name . ' ' . $userBet->user->last_name),
			Yii::$app->urlManagerBackend->createAbsoluteUrl(['user/view', 'id' => $userBet->user->id]),
			['target' => '_blank', 'rel' => 'noopener']
	) ?>
	(<?= Html::a(
			Html::encode($userBet->user->email),
			'mailto:' . Html::encode($userBet->user->email)
	) ?>)
	<br>
</p>

<p>
	<strong>Message:</strong><br>
	<?= nl2br(Html::encode($message)) ?><br>
</p>

<table class="spacer spacer-20"><tr><td>&nbsp;</td></tr></table>

<h2>Bet details:</h2>
<ul class="unstyled">
	<li><strong>Placed:</strong> <?= Yii::$app->formatter->asDatetime($userBet->created_at) ?></li>
	<li><strong>Type:</strong> <?= Html::encode($userBet->oddObj->oddBetType->name) ?></li>
	<li><strong>Option:</strong> <?= Html::encode($userBet->oddObj->name) ?></li>
	<li><strong>Odd:</strong> <?= Html::encode($userBet->odd) ?></li>
	<li><strong>Amount:</strong> <?= Yii::$app->formatter->asCurrencyWithSymbol($userBet->amount) ?></li>
</ul>

<table class="spacer spacer-30"><tr><td>&nbsp;</td></tr></table>

<h2>Match details:</h2>
<ul class="unstyled">
	<li><strong>Date:</strong> <?= Yii::$app->formatter->asDatetime($userBet->oddObj->sportMatch->match_start) ?></li>
	<li>
		<strong>Name:</strong>
		<?= Html::a(
			Html::encode($userBet->oddObj->sportMatch->name),
			Yii::$app->urlManagerBackend->createAbsoluteUrl(['sport-match/view', 'id' => $userBet->oddObj->sportMatch->id]),
			['target' => '_blank', 'rel' => 'noopener']
		) ?>
	</li>
	<li><strong>Home:</strong> <?= $userBet->oddObj->sportMatch->home ?></li>
	<li><strong>Away:</strong> <?= $userBet->oddObj->sportMatch->away ?></li>
</ul>


<?php $this->endContent(); ?>
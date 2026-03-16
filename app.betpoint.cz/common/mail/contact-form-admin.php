<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\ContactForm $contactForm */

?>

<?php $this->beginContent('@common/mail/layouts/html.php'); ?>

<p>Hello,</p>

<p>
	Following is a new message from the Contact form on <?= Html::encode(Yii::$app->name) ?> website:
</p>

<p>
	<strong>User:</strong><br>
	<?= Html::a(
			Html::encode($contactForm->user->first_name . ' ' . $contactForm->user->last_name),
			Yii::$app->urlManagerBackend->createAbsoluteUrl(['user/view', 'id' => $contactForm->user->id]),
			['target' => '_blank', 'rel' => 'noopener']
	) ?>
	(<?= Html::a(
			Html::encode($contactForm->user->email),
			'mailto:' . Html::encode($contactForm->user->email)
	) ?>)
	<br>
</p>

<p>
	<strong>Topic:</strong><br>
	<?= Html::encode($contactForm->topic) ?><br>
</p>

<p>
	<strong>Message:</strong><br>
	<?= nl2br(Html::encode($contactForm->message)) ?><br>
</p>

<?php $this->endContent(); ?>
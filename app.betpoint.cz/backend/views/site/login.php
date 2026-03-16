<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \backend\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
?>
<div class="site-login">
    <div>
        <div class="intro last-child-no-margin">
            <img src="<?= \yii\helpers\Url::to('/images/betpoint-logo.svg') ?>" alt="<?= Yii::$app->name ?>">
        </div>
        <div class="box rounded yellow login-form-container">
            <h2>Sign In to Admin Panel</h2>

            <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['class' => 'last-child-no-margin']]); ?>

                <?= $form->field($model, 'email')
                    ->textInput(['autofocus' => true, 'placeholder' => $model->getAttributeLabel('email')])->label(false) ?>

                <?= $form->field($model, 'password')
                    ->passwordInput(['placeholder' => $model->getAttributeLabel('password')])->label(false) ?>

                <div class="button-container">
                    <?= Html::submitButton('Sign In', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

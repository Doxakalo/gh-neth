<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\SportMatch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sport-match-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_vendor')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'match_start')->textInput() ?>

    <?= $form->field($model, 'home')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'away')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'evaluated')->textInput() ?>

    <?= $form->field($model, 'in_progress')->textInput() ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'extra')->textInput() ?>

    <?= $form->field($model, 'category_id')->textInput() ?>

    <?= $form->field($model, 'sport_id')->textInput() ?>

    <?= $form->field($model, 'season_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

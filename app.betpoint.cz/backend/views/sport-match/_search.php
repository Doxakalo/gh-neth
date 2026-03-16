<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\SportMatchSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sport-match-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_vendor') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'match_start') ?>

    <?= $form->field($model, 'home') ?>

    <?php // echo $form->field($model, 'away') ?>

    <?php // echo $form->field($model, 'evaluated') ?>

    <?php // echo $form->field($model, 'in_progress') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'status_name') ?>

    <?php // echo $form->field($model, 'detail') ?>

    <?php // echo $form->field($model, 'extra') ?>

    <?php // echo $form->field($model, 'category_id') ?>

    <?php // echo $form->field($model, 'sport_id') ?>

    <?php // echo $form->field($model, 'season_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use backend\models\CreateUserForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\User $model */

$this->title = 'Add User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-6">
                <div class="box rounded full-height">
                    <h2>Account details</h2>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-md-6">
                            <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <h2>Comment</h2>

                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'comment', [
                                'options' => ['class' => 'form-group no-margin-bottom']
                            ])->textarea(['maxlength' => true])->label('Internal comment (not visible to user)') ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box rounded full-height">
                    <h2>Login credentials</h2>

                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-md-12">
                            <?= $form->field($model, 'password')->textInput(['maxlength' => true, 'autocomplete' => 'new-password']) ?>
                        </div>
                    </div>

                    <h2>Betcoins</h2>

                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'funds_amount', [
                                'options' => ['class' => 'form-group no-margin-bottom']
                            ])->input('number', [
                                'min' => Yii::$app->params['user.initialFundsMin'],
                                'max' => Yii::$app->params['user.initialFundsMax'],
                                'step' => 1,
                            ])->label('Initial Betcoins amount') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group mt-4">
            <?= Html::submitButton('Create User', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;

/** @var yii\web\View $this */
/** @var common\models\User $userModel */
/** @var backend\models\UserFundsForm $userFundsModel */
/** @var backend\models\ChangePasswordForm $changePasswordModel */

$this->title = 'Edit User: ' . $userModel->getFriendlyName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'user_list_title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $userModel->getFriendlyName(), 'url' => ['view', 'id' => $userModel->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="user-update">

    <div class="page-heading">
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="button-container">
            <?= Html::a('View User', ['view', 'id' => $userModel->id], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-12">
            <div class="box rounded full-height">
                <?php $userUpdateForm = ActiveForm::begin([
                    'id' => 'user-update-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => true,
                    'action' => ['update', 'id' => $userModel->id],
                ]); ?>

                <div class="row">
                    <div class="col-md-6">
                        <h2>Account details</h2>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $userUpdateForm->field($userModel, 'first_name')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $userUpdateForm->field($userModel, 'last_name')->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?= $userUpdateForm->field($userModel, 'nickname')->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?= $userUpdateForm->field($userModel, 'email')->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?= $userUpdateForm->field($userModel, 'status')->dropDownList(User::getStatusLabels())->label('Account Status') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h2>Comment</h2>
                        <?= $userUpdateForm->field($userModel, 'comment')->textarea(['maxlength' => true])->label('Internal comment (not visible to user)') ?>
                    </div>
                </div>

                <div class="button-container">
                    <?= Html::submitButton(Yii::t('app', 'Update Account Details'), ['class' => 'btn btn-success']) ?>
                    <span id="user-update-message" class="form-update-message text-success"></span>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box rounded full-height">
                <h2>Change Password</h2>

                <?php $changePasswordForm = ActiveForm::begin([
                    'id' => 'change-password-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => true,
                    'action' => ['change-password'],
                ]); ?>

                <?= $changePasswordForm->field($changePasswordModel, 'user_id')->hiddenInput(['value' => $userModel->id])->label(false) ?>

                <?= $changePasswordForm->field($changePasswordModel, 'password')->textInput(['maxlength' => true, 'autocomplete' => 'new-password']) ?>

                <div class="button-container">
                    <?= Html::submitButton(Yii::t('app', 'Change Password'), ['class' => 'btn btn-success']) ?>
                    <span id="change-password-message" class="form-update-message text-success"></span>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box rounded full-height">
                <h2>Betcoins</h2>

                <?php $userFundsForm = ActiveForm::begin([
                    'id' => 'user-funds-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => true,
                    'action' => ['update-funds'],
                ]); ?>

                <?= $userFundsForm->field($userFundsModel, 'user_id')->hiddenInput(['value' => $userModel->id])->label(false) ?>

                <p id="funds-current-preview">
                    Current balance: <strong class="nowrap"><?= Yii::$app->formatter->asCurrencyValue($userModel->getFundBalance()) ?></strong>
                </p>

                <div class="row">
                    <div class="col-md-6">
                        <?= $userFundsForm->field($userFundsModel, 'funds_amount')->input('number', [
                            'step' => '1',
                        ])->label('Adjust Amount') ?>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group value-without-label">
                            <p id="funds-new-preview">
                                New Balance: <strong class="nowrap"><?= Yii::$app->formatter->asCurrencyValue($userModel->getFundBalance()) ?></strong>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?= $userFundsForm->field($userFundsModel, 'description')->textarea([
                            'maxlength' => true,
                        ])->label('Description') ?>
                    </div>
                </div>

                <div class="button-container">
                    <?= Html::submitButton(Yii::t('app', 'Adjust Balance'), ['class' => 'btn btn-success']) ?>
                    <span id="funds-update-message" class="form-update-message text-success"></span>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>


<?php
$js = <<<JS

var userUpdateForm = $('#user-update-form');
var userFundsForm = $('#user-funds-form');
var changePasswordForm = $('#change-password-form');

var updatePreviewFundsAmount = function(previewValues) {
    var currentPreview = $('#funds-current-preview strong');
    var newPreview = $('#funds-new-preview strong');

    if(previewValues.currentBalanceFormatted) {
        currentPreview.text(previewValues.currentBalanceFormatted);
    }

    if(previewValues.newBalanceFormatted) {
        newPreview.text(previewValues.newBalanceFormatted);
    }
};

userUpdateForm.on('beforeSubmit', function(e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#user-update-message').text(response.message).show().delay(3000).fadeOut();
            } else if(response.errors) {
                form.yiiActiveForm('updateMessages', response.errors, true);
            }
        },
        error: function() {
            alert('An error occurred while updating the user.');
        }
    });
    return false;
});

userFundsForm.on('beforeSubmit', function(e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                userFundsForm[0].reset();
                if(response.preview) {
                    updatePreviewFundsAmount(response.preview);
                }
                $('#funds-update-message').text(response.message).show().delay(2000).fadeOut();
            } else if(response.errors) {
                form.yiiActiveForm('updateMessages', response.errors, true);
            }
        },
        error: function() {
            alert('An error occurred while adjusting the funds.');
        }
    });
    return false;
});

userFundsForm.on('afterValidate', function (event, messages, errorAttributes) {
    if(messages.preview) {
          updatePreviewFundsAmount(messages.preview);
    }
});

changePasswordForm.on('beforeSubmit', function(e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                changePasswordForm[0].reset();
                $('#change-password-message').text(response.message).show().delay(3000).fadeOut();
            } else if(response.errors) {
                form.yiiActiveForm('updateMessages', response.errors, true);
            }
        },
        error: function() {
            alert('An error occurred while changing password.');
        }
    });
    return false;
});

JS;
$this->registerJs($js, View::POS_READY);
?>
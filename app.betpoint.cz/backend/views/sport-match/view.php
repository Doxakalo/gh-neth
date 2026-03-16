<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\SportMatch $model */
/** @var backend\models\SportMatchResultForm $sportMatchResultForm */
/** @var array|null $currentResultValues */

$this->title = sprintf('Match detail: %s', $model->getFriendlyName());
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'match_list_title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sport-match-view">

    <div class="page-heading">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="row mb-5">
        <div class="col-md-8">
            <div class="box rounded">
                <table class="data-list">
                    <tr>
                        <th>
                            <strong>Match:</strong>
                        </th>
                        <td>
                            <strong><?= $model->home ?></strong> (Home)
                            <strong>/</strong>
                            <strong><?= $model->away ?></strong> (Away)
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <strong>Sport:</strong>
                        </th>
                        <td>
                            <?= $model->sport->name ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <strong>Category:</strong>
                        </th>
                        <td>
                            <?= $model->category->name ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <strong>Date:</strong>
                        </th>
                        <td>
                            <?= Yii::$app->formatter->asDatetime($model->match_start) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <strong>Result:</strong>
                        </th>
                        <td>
                            <?php if ($model->evaluated): ?>
                                <strong id="match-result-score-home"><?= $currentResultValues['home'] ?></strong> (Home)
                                <strong>/</strong>
                                <strong id="match-result-score-away"><?= $currentResultValues['away'] ?></strong> (Away)
                            <?php else: ?>
                                <span class="text-danger">Match result not evaluated yet.</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <?php if ($sportMatchResultForm): ?>
        <div class="row">
            <div class="col-md-8">
                <div id="update-result-launcher">
                    <p id="update-result-info">
                        You can change the match result in case of user complaint.
                    </p>
                    <p id="update-result-message" class="text-success d-none"><strong></strong></p>
                    <button class="btn button-change">Change Result</button>
                </div>

                <div class="box rounded d-none update-result-form-container" id="update-result-form-container">
                    <h2>Change match result</h2>

                    <?php $resultForm = ActiveForm::begin([
                        'id' => 'update-result-form',
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => true,
                        'action' => ['update-result'],
                    ]); ?>

                        <?= $resultForm->field($sportMatchResultForm, 'sport_match_result_id')->hiddenInput()->label(false) ?>

                        <div class="score-inputs">
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $resultForm->field($sportMatchResultForm, 'home_score')->input('number', [
                                        'min' => 0,
                                    ]) ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $resultForm->field($sportMatchResultForm, 'away_score')->input('number', [
                                        'min' => 0,
                                    ]) ?>
                                </div>
                            </div>
                        </div>

                        <p class="text-danger">
                            <strong>
                                Proceed with caution! Changing match results will re-evaluate all bets made on this match.
                            </strong>
                        </p>
                        
                        <div class="button-container">
                            <?= Html::button('Cancel', ['class' => 'btn btn-secondary button-cancel']) ?>
                            <?= Html::submitButton('Confirm Change', ['class' => 'btn btn-success']) ?>
                        </div>

                    <?php ActiveForm::end(); ?>
                </div>

            </div>
        </div>
    <?php endif; ?>

</div>


<?php
$js = <<<JS

var updateResultLauncher = $('#update-result-launcher');
var updateResultForm = $('#update-result-form');
var updateResultFormContainer = $('#update-result-form-container');

var updatePreviewValues = function(previewValues) {
    var homeScore = $('#match-result-score-home');
    var awayScore = $('#match-result-score-away');

    if(previewValues.matchResult) {
        homeScore.text(previewValues.matchResult.home);
        awayScore.text(previewValues.matchResult.away);
    }
};

if(updateResultLauncher.length) {
    var btn = updateResultLauncher.find('.button-change');
    btn.on('click', function() {
        updateResultFormContainer.removeClass('d-none');
        updateResultLauncher.addClass('d-none');
    });
}

if(updateResultForm.length) {
    var btnCancel = updateResultForm.find('.button-cancel');
    btnCancel.on('click', function() {
        updateResultFormContainer.addClass('d-none');
        updateResultLauncher.removeClass('d-none');
        $('#update-result-info').removeClass('d-none');
        $('#update-result-message').addClass('d-none');
        $('#update-result-message strong').text('');
    });

    updateResultForm.on('beforeSubmit', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    if(response.preview) {
                        updatePreviewValues(response.preview);
                    }

                    // show result
                    updateResultFormContainer.addClass('d-none');
                    updateResultLauncher.removeClass('d-none');

                    // update message
                    $('#update-result-info').addClass('d-none');
                    $('#update-result-message').removeClass('d-none');
                    $('#update-result-message strong').text(response.message);

                } else if(response.errors) {
                    form.yiiActiveForm('updateMessages', response.errors, true);
                }
            },
            error: function() {
                alert('An error occurred while updating result.');
            }
        });
        return false;
    });
}

JS;
$this->registerJs($js, View::POS_READY);
?>

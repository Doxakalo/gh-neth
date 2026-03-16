<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use common\utils\formatters\SbcFormatter;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var yii\data\ActiveDataProvider $betsDataProvider */
/** @var yii\data\ActiveDataProvider $transactionsDataProvider */

$this->title = sprintf('User detail: %s', $model->getFriendlyName());
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'user_list_title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="user-view">

    <div class="page-heading">
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="button-container">
            <?= Html::a('Edit User', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-9">
            <div class="box rounded full-height account-details">
                <h2>Account details</h2>

                <div class="details-row">
                    <div>
                        <strong>Email:</strong>
                        <?= Html::a(Html::encode($model->email), 'mailto:' . $model->email) ?>
                    </div>
                    <div>
                        <strong>Created at:</strong>
                        <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
                    </div>
                    <div>
                        <strong>Last active:</strong>
                        <?= $model->last_active_at ? Yii::$app->formatter->asDatetime($model->last_active_at) : 'Never' ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="box rounded yellow full-height account-wallet">
                <h2>Wallet <i class="icon sbc-icon-money"></i></h2>
                <div class="balance">
                    <strong><?= Yii::$app->formatter->asCurrencyValue($model->getFundBalance()); ?></strong>
                    <small>Betcoins</small>
                </div>
            </div>
        </div>
    </div>

    <div class="tabs" id="user-bet-transactions-tabs">
        <div class="tab-list" role="tab-list">
            <button role="tab" class="tab-button">Bets</button>
            <button role="tab" class="tab-button ">Transaction history</button>
        </div>
        <div class="tab-content">
            <div data-tab-content="0" class="bet-list-container">
                <?php Pjax::begin(['id' => 'bet-list']); ?>
                <?= ListView::widget([
                    'dataProvider' => $betsDataProvider,
                    'options' => ['class' => 'bet-list'],
                    'summary' => '',
                    'itemOptions' => ['tag' => false],
                    'itemView' => function ($model, $key) {
                        return $this->render('../components/_bet-item', ['bet' => $model, 'key' => $key]);
                    },
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
            <div data-tab-content="1" class="transaction-list-container">
                <?php Pjax::begin(['id' => 'transaction-list']); ?>
                <table class="transaction-list">
                    <thead>
                        <tr>
                            <th class="action">Action</th>
                            <th class="detail">Detail</th>
                            <th class="date">Date</th>
                            <th class="amount tar">Betcoins</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?= ListView::widget([
                            'dataProvider' => $transactionsDataProvider,
                            'options' => ['tag' => false],
                            'summary' => '',
                            'itemOptions' => ['tag' => false],
                            'itemView' => function ($model, $key) {
                                return $this->render('../components/_transaction-item', ['transaction' => $model, 'key' => $key]);
                            },
                            'layout' => "{items}",
                        ]); ?>
                    </tbody>
                </table>

                <?= \yii\widgets\LinkPager::widget([
                    'pagination' => $transactionsDataProvider->pagination,
                ]) ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
// Register a JS callback for PJAX load
$this->registerJs(<<<JS
    $('#bet-list').on('pjax:end', function() {
        if(typeof window.onUserViewPjaxContentLoad === 'function') {
            window.onUserViewPjaxContentLoad();
        }  
    });
    $('#transaction-list').on('pjax:end', function() {
        if(typeof window.onUserViewPjaxContentLoad === 'function') {
            window.onUserViewPjaxContentLoad();
        }  
    });
JS);
?>

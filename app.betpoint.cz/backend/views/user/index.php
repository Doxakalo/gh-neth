<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <div class="page-heading">
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="button-container">
            <?= Html::a('Add User', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

           [
                'attribute' => 'nickname',
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'Filter by Nickname',
                ],
            ],

            [
                'attribute' => 'full_name',
                'format' => 'raw',
                'headerOptions' => ['class' => 'sort-disabled'],
                'filter' => Html::activeTextInput($searchModel, 'full_name', [
                    'class' => 'form-control',
                    'placeholder' => 'Filter by Name',
                ]),
                'value' => function ($model) {
                    return Html::a(
                        sprintf('%s %s', $model->first_name, $model->last_name),
                        ['view', 'id' => $model->id]
                    );
                },
            ],

           [
                'attribute' => 'email',
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'Filter by Email',
                ],
            ],
            
            [
                'attribute' => 'fundBalance',
                'label' => 'Betcoins',
                'format' => 'raw',
                'filter' => false,
                'contentOptions' => ['class' => 'tar'],
                'value' => function ($model) {
                    return Yii::$app->formatter->asCurrencyValue($model->fundBalance, true);
                },
            ],

            [
                'attribute' => 'created_at',
                'label' => 'Created',
                'format' => ['datetime'],
                'contentOptions' => ['class' => 'nowrap'],
                'filter' => false,
            ],

            [
                'attribute' => 'last_active_at',
                'label' => 'Last Active',
                'format' => 'raw',
                'filter' => false,
                'contentOptions' => ['class' => 'nowrap'],
                'value' => function ($model) {
                    return $model->last_active_at ? Yii::$app->formatter->asDatetime($model->last_active_at) : 'Never';
                },
            ],

			[
				'attribute' => 'status',
				'filter' => Html::activeDropDownList($searchModel, 'status', User::getStatusLabels(), [
					'class' => 'form-control', 
					'prompt' => Yii::t('app', 'grid_filter_select_all'),
				]),
				'value' => function ($model) {
					return $model->statusLabel;
				},				
			],

            [
                'class' => ActionColumn::class,
                'template' => '{view} {update}',
                'urlCreator' => function ($action, User $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

</div>

<?php

use common\models\SportMatch;
use common\models\Sport;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\SportMatchSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sport Matches';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sport-match-index">

    <div class="page-heading">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute' => 'sportName',
                'label' => 'Sport',
                'value' => 'sport.name',
                'filter' => \yii\helpers\ArrayHelper::map(
                    Sport::find()->orderBy('name')->all(),
                    'name',
                    'name'
                ),
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => Yii::t('app', 'grid_filter_select_all')
                ],
            ],

            [
                'attribute' => 'categoryName',
                'label' => 'Category',
                'value' => 'category.name',
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'Filter by Category',
                ],
            ],

            [
                'attribute' => 'name',
                'label' => 'Match Name',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(
                        Html::encode($model->name),
                        ['view', 'id' => $model->id]
                    );
                },
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'Filter by Match Name',
                ],
            ],

            [
                'attribute' => 'match_start',
                'format' => 'datetime',
                'filter' => false,
            ],

            [
                'attribute' => 'evaluated',
                'filter' => Html::activeDropDownList($searchModel, 'evaluated', [
                    0 => Yii::t('app', 'state_value_no'),
                    1 => Yii::t('app', 'state_value_yes')
                ], [
                    'class' => 'form-control',
                    'prompt' => Yii::t('app', 'grid_filter_select_all')
                ]),
                'value' => function ($model) {
                    return $model->evaluated === 1 ? Yii::t('app', 'state_value_yes') : Yii::t('app', 'state_value_no');
                },
            ],

            [
                'class' => ActionColumn::class,
                'template' => '{view}',
                'urlCreator' => function ($action, SportMatch $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

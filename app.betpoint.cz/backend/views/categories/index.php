<?php

use common\models\Sport;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\CategorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Categories allower';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sport-match-index">

    <div class="page-heading d-flex align-items-center gap-2">
        <h1 class="m-0"><?= Html::encode($this->title) ?></h1>
        
    </div>
    <div>
        <span class="text-muted d-flex align-items-center ">     
            <span class="mt-3 mb-3">New categories may take up to 4 hours to appear after being enabled.</span>
        </span>
    </div>

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
                    'id',
                    'name'
                ),
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => Yii::t('app', 'grid_filter_select_all')
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
                'attribute' => 'country_name',
                'label' => 'Country Name',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(
                        Html::encode($model->country_name),
                        ['view', 'id' => $model->id]
                    );
                },
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'Filter by Country',
                ],
            ],

           [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'enabled',
                'label' => 'Enabled',
                'format' => 'raw',
                'headerOptions' => [
                    'style' => 'text-align: center; vertical-align: middle;'
                ],
                'contentOptions' => [
                    'style' => 'text-align: center; vertical-align: middle;'
                ],
                'filter' => Html::activeDropDownList($searchModel, 'enabled', [
                    1 => Yii::t('app', 'state_value_yes'),
                    0 => Yii::t('app', 'state_value_no')
                ], [
                    'class' => 'form-control',
                    'prompt' => Yii::t('app', 'grid_filter_select_all'),
                    'style' => ' text-align: center;'
                ]),
                'value' => function($model) {
                    $formId = 'edit-form-' . $model->id;

                    return Html::beginForm(['categories/enable-disable'], 'post', ['id' => $formId]) .
                        Html::hiddenInput('id', $model->id) .
                        Html::hiddenInput('vendor_id', $model->id_vendor) .
                        Html::hiddenInput('sport_id', $model->sport_id) .
                        Html::hiddenInput('sport', $model->sport->name) .
                        Html::hiddenInput('country_name', $model->country_name) .
                        Html::checkbox('enabled', $model->enabled == 1, [
                            'value' => 1,
                            'uncheck' => 0,
                            'onchange' => "document.getElementById('$formId').submit()",
                            'style' => 'width:13px; height:13px; transform:scale(1.4); cursor:pointer;',
                        ]) .
                        Html::endForm();
                },
            ],


        ],
    ]); ?>

</div>

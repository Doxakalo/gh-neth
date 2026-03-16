<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\SportMatch $model */

$this->title = 'Update Sport Match: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Sport Matches', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sport-match-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\SportMatch $model */

$this->title = 'Create Sport Match';
$this->params['breadcrumbs'][] = ['label' => 'Sport Matches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sport-match-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

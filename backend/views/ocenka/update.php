<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Ocenka */

$this->title = 'Update Ocenka: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Ocenkas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ocenka-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

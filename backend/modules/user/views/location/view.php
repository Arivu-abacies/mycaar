<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Edit', ['update', 'id' => $model->location_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->location_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
		 <?= Html::a('All Locations', ['index'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'company.name',	
            'name',
        ],
    ]) ?>

</div>

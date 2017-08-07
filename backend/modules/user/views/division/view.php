<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Division */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Divisions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="division-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Edit', ['update', 'id' => $model->division_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->division_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
		 <?= Html::a('All Divisions', ['index'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'company.name',			
            'title',
            'description',						
        ],
		
    ]) ?>

</div>

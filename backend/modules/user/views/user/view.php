<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\MyCaar;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->email;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
		
		 <?= Html::a('All Users', ['index'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [        
			'userProfile.firstname',
			'userProfile.lastname',
			[
				 'label'=>'Username / Email ID',
				 'value' =>$model->email,
			],
			[
				'attribute' => 'role', 
				'label' => 'User Access Level',
				'value' => MyCaar::getRoleNameByUserid($model->id),
			],
			[
				'attribute' => 'company.name', 
				'label' => 'Company Name',
				'value' => isset($model->company->name)?$model->company->name:'( not Set )',
			],
			
        ],
    ]) ?>

</div>

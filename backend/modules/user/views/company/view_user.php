<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\MyCaar;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->firstname;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Edit', ['update-user', 'id' => $model->user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete-user', 'id' => $model->user_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this User?',
                'method' => 'post',
            ],
        ]) ?>
		 <?= Html::a('All Users', ['index-user'], ['class' => 'btn btn-success']) ?>
    </p>
		
		
	
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'firstname',
            'lastname',
			[
				 'label'=>'Username / Email ID',
				 'value' =>$model->user->email,
			],
			
			[
				'attribute' => 'role', 
				'label' => 'Access Level',
				'value' => MyCaar::getRoleNameByUserid($model->user_id),
			],			
			'employee_number',			
			'divisionModel.title',
			'locationModel.name',
			'stateModel.name',
			[
				'label' => 'Role',
				'value' => isset($model->roleModel->title) ? $model->roleModel->title : '(not set)',
			]			
        ],
    ]) ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\MyCaar;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = "My Profile";
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Edit My Profile', ['update-my-profile'], ['class' => 'btn btn-primary']) ?>       
    </p>
		
		
	
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

           // 'firstname',
           // 'lastname',
          	[
				'attribute' => 'firstname',
				'label' => 'First Name',
							
			],	
			[
				'attribute' => 'lastname',
				'label' => 'Last Name',
							
			],
			[
				 'label'=>'User Name / Email ID',
				 'value' =>$model->user->email,
			],
			
			[
				'attribute' => 'role', 
				'label' => 'User Access Level',
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

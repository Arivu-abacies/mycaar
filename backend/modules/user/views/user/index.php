<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\MyCaar;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchUser */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>
<div class="card">

    <div class="card-body">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	 <div class="row">
		<div class="col-md-6">
			<?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
		</div>	
		<div class="col-md-6">
		  <?php if(\Yii::$app->user->can('company_admin')) { ?>
			<?= Html::a('Import User', ['importuser/importexcel'], ['class' => 'btn btn-info pull-right']) ?>
		  <?php } ?>
		
			<a class="btn btn-danger pull-right" style="margin-right: 10px;" id="multi_delete" name="multi_delete" >Multi Delete</a>
		</div>
		
</div>	 
  
  <?php
	//echo "<pre>";
	//print_r(MyCaar::getChildRoles('company_admin'));
	//print_r(ArrayHelper::map(MyCaar::getChildRoles('company_admin'), 'key', 'value'));
	//exit;
  ?>
	
	<div class="small-padding"></div>
	
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

			[	
					'class' => 'yii\grid\CheckboxColumn',
					 'checkboxOptions' => function ($data){
						return ['checked' =>false,'value'=>$data['id']];
					}, 
				
			],	
			[
				'attribute' => 'firstname',
                                'label' => 'First Name',
				'value' => 'userProfile.firstname',				
			],	
			[
				'attribute' => 'lastname',
                                'label' => 'Last Name',
				'value' => 'userProfile.lastname',				
			],								
			[
				'attribute' => 'roleName',
				'value' => 'roleName',
			 	 'filter' => Html::activeDropDownList($searchModel, 'roleName',MyCaar::getChildRoles(MyCaar::getRoleNameByUserid(Yii::$app->user->identity->id)),['class'=>'form-control input-sm','prompt' => 'Role Name']), 
/*
			'filter' => Html::activeDropDownList($searchModel, 'roleName',MyCaar::getChildRolesName(MyCaar::getRoleNameByUserid(Yii::$app->user->identity->id)),['class'=>'form-control input-sm','prompt' => 'Role Name']),				 
		*/		
			],
             ['label' => 'User Name / Email ID',
				'attribute' => 'email',			
			 ],
			
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
	</div>
</div>



 <script type = "text/javascript">
 
     $(document).ready(function(){	
	 
		$("#multi_delete").click(function(){				 
		    var r = confirm("Are you Sure To Delete!");
		    if (r == true) {
				var user_id = $.map($('input[name="selection[]"]:checked'), function(c){return c.value; })
				if($.trim(user_id) === "")
				 {
					alert("Please Select the Checkbox to Delete!!!.");
					return false;
				  }				
				 $.ajax({
				   url: '<?=Yii::$app->homeUrl."user/user/multi-delete"?>',
				   type: 'POST',
				   data: {  user_id: user_id,
				   },
				   success: function(data) {												
						location.reload();
				   }
				 }); 
				}		
			 });			 			 
	});
		 
      </script> 
	  
	   

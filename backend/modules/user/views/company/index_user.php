<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\MyCaar;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchUser */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Url;
use common\models\User;

use common\models\Role;
use common\models\Division;
use common\models\Location;
use common\models\State;

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(\Yii::$app->homeUrl."css/custom/w3.css");
$firstname = isset($params['firstname'])?$params['firstname']:'';
$lastname = isset($params['lastname'])?$params['lastname']:'';
$email = isset($params['email'])?$params['email']:'';
$selected_rolename = isset($params['roleName'])?$params['roleName']:'';

$selected_division = isset($params['division'])?$params['division']:'';
$selected_role = isset($params['role'])?$params['role']:'';
$selected_location = isset($params['location'])?$params['location']:'';
$selected_state = isset($params['state'])?$params['state']:''; 


?>
<input type="hidden" id="search_field_val" name="search_field_val" value='<?php echo serialize($params); ?>' />
<h1><?= Html::encode($this->title) ?></h1>
<div class="card">

    <div class="card-body">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

 <div class="row">
		<div class="col-md-6">
			<?= Html::a('Create User', ['create-user'], ['class' => 'btn btn-success']) ?>
		</div>	
		<div class="col-md-6">
		  <?php if(\Yii::$app->user->can('company_admin')) { ?>
			<?= Html::a('Import User', ['importuser/importexcel'], ['class' => 'btn btn-info pull-right']) ?>
		  <?php } ?>
		  
		  	<a class="btn btn-danger pull-right" style="margin-right: 10px;" id="multi_delete" name="multi_delete" >Multi Delete</a>
			
		</div>	 
</div>	 
  <div class="row" style="height:20px">
  </div>	
  <div class="card card-collapse">
			<div class="card-head style-default">
				<div class="tools">
					<div class="btn-group">
						<a class="btn btn-icon-toggle btn-collapse" data-toggle="collapsed"><i class="fa fa-angle-down"></i></a>
					</div>
				</div>
				<header>Search</header>
			</div><!--end .card-head -->
			<div class="card-body" style="display:none">
				<div class="program-search">
					<form method="post" >
						<div class="row">
							<div class="col-sm-3">
									<div class="form-group">
									<label class="control-label" for="searchreport-c_id">First Name</label>
									<input type="text" class="form-control" name="firstname" value="<?=$firstname?>">
									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
									<div class="form-group">
									<label class="control-label" for="searchreport-c_id">Last Name</label>
									<input type="text" class="form-control" name="lastname" value="<?=$lastname?>">
									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
									<div class="form-group">
									<label class="control-label" for="searchreport-c_id">User Name / Email ID</label>
									<input type="text" class="form-control" name="email" value="<?=$email?>">
									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
							<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Access Level</label>
									
									<?= Html::dropDownList('roleName', "$selected_rolename",MyCaar::getChildRoles('company_admin'),['prompt'=>'--Select--','class'=>'form-control']) ?> 
									
									 
									<div class="help-block"></div>
								</div>
							</div>
								
						</div>
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Role</label>

									<?= Html::dropDownList('role', "$selected_role",ArrayHelper::map(Role::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->orderBy('title')->all(), 'role_id', 'title'),['prompt'=>'--Select--','class'=>'form-control']) ?>

									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Division</label>

									<?= Html::dropDownList('division', "$selected_division",ArrayHelper::map(Division::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->orderBy('title')->all(), 'division_id', 'title'),['prompt'=>'--Select--','class'=>'form-control']) ?>

									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Location</label>

									<?= Html::dropDownList('location', "$selected_location",ArrayHelper::map(Location::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->orderBy('name')->all(), 'location_id', 'name'),['prompt'=>'--Select--','class'=>'form-control']) ?>

									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">State</label>

									<?= Html::dropDownList('state', "$selected_state",ArrayHelper::map(State::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->orderBy('name')->all(), 'state_id', 'name'),['prompt'=>'--Select--','class'=>'form-control']) ?>

									<div class="help-block"></div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary">Search</button>  
							<a class="btn btn-danger" href="<?php echo Url::to(['company/index-user'])?>" >Clear Search </a>
						</div>
					</form>
				</div>
			</div><!--end .card-body -->
		</div><!--end .card -->
	
	<div class="row">
		<div class="col-sm-3">
			<div class="form-group">
				<label class="control-label" for="searchreport-user_id">Change Access Level</label>
					<?= Html::dropDownList('changeroleName', "",MyCaar::getChildRoles('company_admin'),['prompt'=>'-- Change Access Level to --','class'=>'form-control','id'=>'changeroleName']) ?> 
				<div class="help-block"></div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
					<a class="btn btn-primary pull-left" style="margin-top: 20px;" id="multi_change" name="multi_change" >Change</a>
			</div>
		</div>
	</div>
	
	<div class="small-padding"></div>
	<?php

	
	?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
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
				'label'=>'Access Level',
			 	 'filter' => Html::activeDropDownList($searchModel, 'roleName',MyCaar::getChildRoles('company_admin'),['class'=>'form-control input-sm','prompt' => 'Role Name']),   			
			],
			[
				'attribute' => 'role',
				'value' =>'userRole.title',	
			],	
			
			/* [
				'attribute' => 'role',
				'label'=>'Position',	
				'value' => function ($dataProvider){
						$role = Role::findOne($dataProvider->userProfile->role);
						return ($role)?$role->title:" (not set) ";
					},	
			], */
			
            ['label' => 'User Name / Email ID',
				'attribute' => 'email',			
			 ],			
		
			[
  'class' => 'yii\grid\ActionColumn',
  'template' => '{view}{update}{delete}',
  'buttons' => [
    'view' => function ($url, $model) {
        return Html::a('<span style="margin-left:5px" class="glyphicon glyphicon-eye-open"></span>', 'view-user?id='.$model->id, [
                    'title' => Yii::t('app', 'View'),
        ]);
    },
	'update' => function ($url, $model) {
        return Html::a('<span style="margin-left:5px" class="glyphicon glyphicon-pencil"></span>', 'update-user?id='.$model->id, [
                    'title' => Yii::t('app', 'Update'),
        ]);
    },
	'delete' => function ($url, $model) {
        return Html::a('<span style="margin-left:5px" class="glyphicon glyphicon-trash"></span>', 'delete-user?id='.$model->id, [
                    'title' => Yii::t('app', 'Delete'),
					'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                   ],
        ]);
    },
  ],
],
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
				   url: '<?=Yii::$app->homeUrl."user/company/multi-delete-user"?>',
				   type: 'POST',
				   data: {  user_id: user_id,
				   },
				   success: function(data) {		
						location.reload();
				   }
				 }); 
				}		
			 });

		$("#multi_change").click(function(){		
			
			var role = $("#changeroleName").val();
			if($.trim(role) == "")
			{
				alert("Please Select the DropDown to Change the Role!!!.");
				return false;
			}
		
			var user_id = $.map($('input[name="selection[]"]:checked'), function(c){return c.value; })
			if($.trim(user_id) === "")
			{
				alert("Please Select the Checkbox to Change the Role!!!.");
				return false;
			}	
				  
		    var r = confirm("Are you Sure To Change the Role!");
		    if (r == true) {
							
				 $.ajax({
				   url: '<?=Yii::$app->homeUrl."user/company/multi-change-role"?>',
				   type: 'POST',
				   data: {  user_id:$.trim(user_id),role:$.trim(role),
				   },
				   success: function(data) {
						location.reload();
				   }
				 }); 
				}		
			 });
			
		$(".pagination li a").click(function(){			
				var test = $(this).attr("href");
				var new_url = $("#search_field_val").val();				 
				$(this).attr("href", test+"&data="+new_url);
			   			
		});	 
	});
		 
      </script> 
	  <script>
		//$('.card-head .tools .btn-collapse').on('click', function (e) {
		$('.card-head').on('click', function (e) {
			var card = $(e.currentTarget).closest('.card');
			materialadmin.AppCard.toggleCardCollapse(card);
		});
		
	</script>

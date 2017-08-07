<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\MyCaar;
use common\models\Program;

use yii\helpers\Url;
use common\models\User;

use common\models\Role;
use common\models\Division;
use common\models\Location;
use common\models\State;


$selected_company = \Yii::$app->user->identity->c_id;

if(Yii::$app->user->can("company_assessor")){
	$location = Location::find()->where(['company_id'=>$selected_company])->orderBy('name')->all();
	}
else if(Yii::$app->user->can("group_assessor")){
	$access_location = \Yii::$app->user->identity->userProfile->access_location;
	if(!empty($access_location))
	 $useraccesslocation = explode(",",$access_location);
 
	$getlocation = Location::find()->where(['company_id'=>$selected_company])->orderBy('name')->all();
	foreach($getlocation as $key=>$get)
	{
		if(isset($useraccesslocation) && in_array($get->location_id,$useraccesslocation))
		{
		 $location[$key]['location_id']= $get->location_id;
		 $location[$key]['name']= $get->name;
		}
	}	
}
else if(Yii::$app->user->can("local_assessor")){
	$locationid = \Yii::$app->user->identity->userProfile->location;
	$location = Location::find()->where(['company_id'=>$selected_company,'location_id'=>$locationid])->orderBy('name')->all();
}

$program_id = isset($params['Program'])?$params['Program']:'';
$username = isset($params['username'])?$params['username']:'';
$fullname = isset($params['fullname'])?$params['fullname']:'';
$enrollcheck = isset($params['enrollcheck'])?$params['enrollcheck']:'';
$accesslevel = isset($params['accesslevel'])?$params['accesslevel']:'';

$selected_division = isset($params['division'])?$params['division']:'';
$selected_role = isset($params['role'])?$params['role']:'';
$selected_location = isset($params['location'])?$params['location']:'';
$selected_state = isset($params['state'])?$params['state']:''; 

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchUser */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Enroll User';
$this->params['breadcrumbs'][] = $this->title;

	$array = [
					['id' => '1', 'name' => 'Enrolled'],
					['id' => '0', 'name' => 'Not Enrolled'],
				 ];
				 
?>
<h1>Enroll Users</h1>
<div class="small-padding">
<div class="card">

<div class="card-body">
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
							<div class="col-md-3" >
							<div class="form-group">
								<label class="control-label" for="searchreport-unit_id">Program</label>
								<?=Html::dropDownList(
									'Program',
									"$program_id",
									ArrayHelper::map(Program::find()->where(['company_id' =>Yii::$app->user->identity->c_id])->orderBy('title')->all(),'program_id', 'title'),
									['prompt'=> '--Select--',
									 'id' => 'program_select', 'class' => 'form-control']
								);?>
								<div class="help-block"></div>								
							</div>
						</div>
						<div class="col-sm-3">
									<div class="form-group">
									<label class="control-label" for="searchreport-c_id">Fullname</label>
									<input type="text" class="form-control" name="fullname" value="<?=$fullname?>">
									<div class="help-block"></div>
								</div>
							</div>
						
						<div class="col-sm-3">
									<div class="form-group">
									<label class="control-label" for="searchreport-c_id">User Name</label>
									<input type="text" class="form-control" name="username" value="<?=$username?>">
									<div class="help-block"></div>
								</div>
						</div>
						
						<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Enroll-status</label>

									<?= Html::dropDownList('enrollcheck',"$enrollcheck",  ArrayHelper::map($array, 'id', 'name'),['class'=>'form-control','prompt' => '--Select--'])  ?>

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

									<?= Html::dropDownList('location', "$selected_location",ArrayHelper::map($location, 'location_id', 'name'),['prompt'=>'--Select--','class'=>'form-control']) ?>

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
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Access Level</label>
									
									<?= Html::dropDownList('accesslevel', "$accesslevel",MyCaar::getChildRoles('company_admin'),['prompt'=>'--Select--','class'=>'form-control']) ?>
									
									<div class="help-block"></div>
								</div>
							</div>
						</div>	
						<div class="form-group">
							<button type="submit" value="search" name="clickaction" class="btn btn-primary searchclick">Search</button>  
							<a class="btn btn-danger" href="<?php echo Url::to(['company/enroll-user'])?>" >Clear Search </a>
						</div>
					<!--</form>-->
				</div>
			</div><!--end .card-body -->
		</div><!--end .card -->
	
	


	<?php //Html::beginForm('enroll-user-save','post');?>
	
	<div class="row">	
		
		
		<div class="col-md-5" >
		  <div class="form-group">
			<label class="control-label" for="searchreport-unit_id">Mark to Status</label>
		<?=Html::dropDownList('action','',[''=>'Mark selected as: ','enrolled'=>'Enrolled','unenrolled'=>'Not Enrolled'],['class'=>'form-control','id'=>'action'])?>
			<div class="help-block"></div>								
		  </div>
		</div>
		<div class="col-md-2" >
		  <div class="form-group">
			<label class="control-label" for="searchreport-unit_id"></label>
			<?=Html::submitButton('Change', ['class' => 'btn btn-success changeclick pull-right',"value"=>"change","name"=>"clickaction","style"=>"margin-top: 20px;"]);?>						
		  </div>
		</div>
	</div>
	


<div class="small-padding"></div>
		<?php if($program_id){ 
?>				
				<?=GridView::widget([
				'dataProvider' => $dataProvider,
				//'filterModel' => $searchModel,
				'layout' => '{items}',
				'columns' => [
					[	
					'class' => 'yii\grid\CheckboxColumn',
					 'checkboxOptions' => function ($data){
						return ['checked' =>false,'value'=>$data['id']];
					}, 
				
					],
					['class' => 'yii\grid\SerialColumn'],									
					[
					  'label' => 'Name',	
					  'attribute'=>'fullname',
					  'value'=>'fullname'
					], 				
					'username', 				
					[
						'format' => 'html',
						'label' => 'Status',					 
						'filter' => Html::activeDropDownList($searchModel, 'enrollcheck', ArrayHelper::map($array, 'id', 'name'),['class'=>'form-control input-sm','prompt' => '--Status--']),
						 'value' => function ($data){
						return ($data['is_enrolled']==1)?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-close text-danger"></i>';
						} 					
					],					
				],
				]);  ?>
		<?php } ?>
	<?= Html::endForm();?> 
</div>
	
	</div>	
</div>	
</div>
<script>		
		$( ".changeclick" ).click(function() {
			var program = $("#program_select").val();
			if($.trim(program) == "")
			{
				alert('Please Select the "Program in dropbox" !!!.');
				return false;
			}
			var action = $("#action").val();
			if($.trim(action) == "")
			{
				alert('Please Select the option in dropbox "Mark selected as" !!!.');
				return false;
			}	
			var user_id = $.map($('input[name="selection[]"]:checked'), function(c){return c.value; })
			if($.trim(user_id) === "")
			{
				alert("Please Select the Checkbox to Enroll / Not Enroll !!!.");
				return false;
			}	
			
		});
			
			
		$( ".searchclick" ).click(function() {
			var program = $("#program_select").val();
			if($.trim(program) == "")
			{
				alert('Please Select the "Program in dropbox" !!!.');
				return false;
			}
			
		});
</script>		

 <script>
		//$('.card-head .tools .btn-collapse').on('click', function (e) {
		$('.card-head').on('click', function (e) {
			var card = $(e.currentTarget).closest('.card');
			materialadmin.AppCard.toggleCardCollapse(card);
		});
		
</script>			
<style>
	.card-head{
		cursor:pointer
	}
</style>
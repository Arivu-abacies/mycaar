<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

use common\models\Role;
use common\models\Division;
use common\models\Location;
use common\models\State;
use common\models\MyCaar;
use common\models\Program;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchUnit */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Assessor Reports';
$this->params['breadcrumbs'][] = $this->title;

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


$accesslevel = isset($params['accesslevel'])?$params['accesslevel']:'';
$programid = isset($params['programid'])?$params['programid']:'';
$moduleid = isset($params['moduleid'])?$params['moduleid']:'';
$unitid = isset($params['unitid'])?$params['unitid']:'';
$selected_division = isset($params['division'])?$params['division']:'';
$selected_role = isset($params['role'])?$params['role']:'';
$selected_location = isset($params['location'])?$params['location']:'';
$selected_state = isset($params['state'])?$params['state']:''; 

?>
<div class="unit-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<div class="card card-collapse">
			<div class="card-head style-default">
				<div class="tools">
					<div class="btn-group">
						<a class="btn btn-icon-toggle btn-collapse" data-toggle="collapsed"><i class="fa fa-angle-down"></i></a>
					</div>
				</div>
				<header>Search</header>
			</div><!--end .card-head -->
			<div class="card-body" style="display:none" >
				<div class="program-search">
					<form method="get" >
						
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
									<label class="control-label" for="searchreport-user_id">Access Level [ student ]</label>
									
									<?= Html::dropDownList('accesslevel', "$accesslevel",MyCaar::getChildRoles('company_admin'),['prompt'=>'--Select--','class'=>'form-control']) ?>
									
									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Program</label>
									
									<?= Html::dropDownList('programid', "$programid",ArrayHelper::map(Program::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->orderBy('title')->all(), 'program_id', 'title'),['prompt'=>'--Select--','class'=>'form-control','id'=>'programid']) ?>
									
									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Module</label>
									
									<?= Html::dropDownList('moduleid', "$moduleid",array('' => '--Select--'),['class'=>'form-control','id'=>'moduleid']) ?>
									
									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Lesson</label>
									
									<?= Html::dropDownList('unitid', "$unitid", array('' => '--Select--'),['class'=>'form-control','id'=>'unitid']) ?>
									
									
					
									<div class="help-block"></div>
								</div>
							</div>
						</div>	
						<div class="form-group">
							<button type="submit" value="search" name="clickaction" class="btn btn-primary searchclick">Search</button>  
							<a class="btn btn-danger" href="<?php echo Url::to(['report/assessor-report'])?>" >Clear Search </a>
						</div>
					</form>
				</div>
			</div><!--end .card-body -->
		</div><!--end .card -->
	
	

	
    <p>
        <?= Html::a('Dashboard', ['/#db'], ['class' => 'btn btn-success']) ?>
    </p>

	<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'columns' => [
				['class' => 'yii\grid\SerialColumn'],

				//'unit_id',
				//'unit.title',
				
				[
					'label' => 'Assessor',
					'attribute' => 'cap_done_by',
					'value' => 'assessor.fullname',
				
				],
				
				[
					'label' => 'Student',
					'attribute' => 'student_id',
					'value' => 'student.fullname',
				
				],
				
				[
					'label' => 'Module',
					'attribute' => 'module_id',
					'value' => 'unit.module.title',
				
				],
				[
					'label' => 'Lesson',
					'attribute' => 'unit_id',
					'value' => 'unit.title',
				
				],
				[
					'attribute' => 'updated_at',
					'value' => 'updated_at',
					'filter' => '<input placeholder="--Search--" type="text" name="SearchUnitReport[updated_at]" id="updated_at" class="form-control" />',
					'format' => 'html',
				],
				//'assessor.fullname',
				
				[
					'attribute' => 'capability_progress',
					'format' => 'raw',
					'value' => function($data){
						if($data->capability_progress== 100)
							$color = "green";
						else $color = "amber";
						//switch($color){
						//	case "amber":
								return "<div class='td-{$color}'>$color</div>";
						//}
						//return $data->capability_progress;
					}
				],
				//'capability_progress',
				

				//['class' => 'yii\grid\ActionColumn'],
			],
		]); ?>
</div>

 <script>
 //$(document).ready(function(){
	  	//$('.card-head .tools .btn-collapse').on('click', function (e) {
	  	$('.card-head').on('click', function (e) {
			var card = $(e.currentTarget).closest('.card');
			materialadmin.AppCard.toggleCardCollapse(card);

		}); 

	$('#updated_at').datepicker({
	//$(document).find('#updated_at').datepicker({
		autoclose: true, 
		todayHighlight: true,
		format: 'yyyy-mm-dd',
		clearBtn: true
	});		
 //});		
 
 $("#programid").change(function(){
		var program_id = $(this).val();
		 $.ajax({
				   url: '<?=Url::to(['report/get-modules'])?>',
				   type: 'GET',
				   data: { p_id: program_id,
				   },
				   success: function(data) {		
						$("#moduleid").html(data);						
				   }
				 }); 		
    });
	
 $("#moduleid").change(function(){
		var module_id = $(this).val();
		 $.ajax({
				   url: '<?=Url::to(['report/get-units'])?>',
				   type: 'GET',
				   data: { m_id: module_id,
				   },
				   success: function(data) {		
						$("#unitid").html(data);						
				   }
				 }); 		
    });	
<?php if(isset($moduleid) && !empty($moduleid)){ ?>
			$.ajax({
				   url: '<?=Url::to(['report/get-modules'])?>',
				   type: 'GET',
				   data: { p_id: <?php echo $programid; ?>,m_id: <?php echo $moduleid; ?>
				   },
				   success: function(data) {		
						$("#moduleid").html(data);						
				   }
				 }); 
				 
<?php } ?>	
<?php if(isset($unitid) && !empty($unitid)){ ?>
			$.ajax({
				   url: '<?=Url::to(['report/get-units'])?>',
				   type: 'GET',
				   data: { m_id: <?php echo $moduleid; ?>,u_id: <?php echo $unitid; ?>
				   },
				   success: function(data) {		
						$("#unitid").html(data);						
				   }
				 }); 
				 
<?php } ?>	
</script>	
<style>
	.card-head{
		cursor:pointer
	}
</style>


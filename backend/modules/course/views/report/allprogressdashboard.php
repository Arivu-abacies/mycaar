<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\helpers\Url;

use common\models\Company;
use common\models\User;
use common\models\Program;

use common\models\Role;
use common\models\Division;
use common\models\Location;
use common\models\State;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reports';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(\Yii::$app->homeUrl."css/custom/w3.css");
//if($params){
if(Yii::$app->user->can("superadmin"))
	$selected_company = isset($params['company'])?$params['company']:\Yii::$app->user->identity->c_id;
else
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


/* 	$selected_user = isset($params['user'])?$params['user']:'';
	$selected_program = isset($params['program'])?$params['program']:'';
	$firstname = isset($params['firstname'])?$params['firstname']:'';
	$lastname = isset($params['lastname'])?$params['lastname']:'';
	$role = isset($params['role'])?$params['role']:'';
	$division = isset($params['division'])?$params['division']:'';
	$location = isset($params['location'])?$params['location']:'';
	$state = isset($params['state'])?$params['state']:'';
	$selected_page = isset($params['page'])?$params['page']:0; */
	
	$selected_user = isset($params['user'])?$params['user']:'';
	$selected_program = isset($params['program'])?$params['program']:'';
	$firstname = isset($params['firstname'])?$params['firstname']:'';
	$lastname = isset($params['lastname'])?$params['lastname']:'';
	$selected_role = isset($params['role'])?$params['role']:'';
	$selected_division = isset($params['division'])?$params['division']:'';
	$selected_location = isset($params['location'])?$params['location']:'';
	$selected_state = isset($params['state'])?$params['state']:'';
	$selected_page = isset($params['page'])?$params['page']:0;
	
	

?>
<script src="<?=Yii::$app->homeUrl;?>js/js/pie-chart.js" type="text/javascript"></script>
<link href="<?=Yii::$app->homeUrl;?>js/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<h4></h4>

		<div class="card card-collapse card-collapsed small-padding">
			<div class="card-head card-head-xs style-default">
				<div class="tools">
					<div class="btn-group">
						<a class="btn btn-icon-toggle btn-collapse" data-toggle="collapse"><i class="fa fa-angle-down"></i></a>
					</div>
				</div>
				<header>Search</header>
			</div><!--end .card-head -->
			<div class="card-body" style="display:none">
				<div class="program-search">
					<form method="post" name="formsubmit" id="formsubmit" >
						<div class="row">
							<!--<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">User ID</label>
									<?php //echo Html::dropDownList('user', "$selected_user",ArrayHelper::map(User::find()->where(['c_id'=>\Yii::$app->user->identity->c_id,'status'=>10])->all(), 'id', 'username'),['prompt'=>'--Select--','class'=>'form-control']) ?>
									<div class="help-block"></div>
								</div>
							</div>-->
						<?php if(Yii::$app->user->can("superadmin")){  ?>	
							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label" for="searchreport-cmp_id">Company</label>
									<?= Html::dropDownList('company', "$selected_company",ArrayHelper::map(Company::find()->orderBy('name')->all(), 'company_id', 'name'),['prompt'=>'--Select--','class'=>'form-control','id'=>'company','required' => 'required',
										  'onchange'=>'
												$.post( "'.Yii::$app->urlManager->createUrl(		'course/program/get-program?c_id=').'"+$(this).val(), function( data ) {
													$( "select#program" ).html( data ).change();
														});
														
												$.post( "'.Yii::$app->urlManager->createUrl('user/role/get-role?c_id=').'"+$(this).val(), function( data ) {
													$( "select#role" ).html( data ).change();
														});
												
												$.post( "'.Yii::$app->urlManager->createUrl('user/division/get-division?c_id=').'"+$(this).val(), function( data ) {
													$( "select#division" ).html( data ).change();
														});
												
												$.post( "'.Yii::$app->urlManager->createUrl('user/location/get-location?c_id=').'"+$(this).val(), function( data ) {
													$( "select#location" ).html( data ).change();
														});

												$.post( "'.Yii::$app->urlManager->createUrl('user/state/get-state?c_id=').'"+$(this).val(), function( data ) {
													$( "select#state" ).html( data ).change();
														});
														
												'] 
											) ?>
									<div class="help-block"></div>
								</div>
							</div>
					<?php } ?>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label" for="searchreport-unit_id">Program</label>
									<?= Html::dropDownList('program', "$selected_program",ArrayHelper::map(Program::find()->where(['company_id'=>$selected_company])->orderBy('title')->all(), 'program_id', 'title'),['prompt'=>'--Select--','class'=>'form-control','id'=>'program']) ?>
									<div class="help-block"></div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label" for="searchreport-c_id">First Name</label>
									<input type="text" class="form-control" name="firstname" value="<?=$firstname?>">
									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label" for="searchreport-c_id">Last Name</label>
									<input type="text" class="form-control" name="lastname" value="<?=$lastname?>">
									<div class="help-block"></div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Role</label>

									<?= Html::dropDownList('role', "$selected_role",ArrayHelper::map(Role::find()->where(['company_id'=>$selected_company])->orderBy('title')->all(), 'role_id', 'title'),['prompt'=>'--Select--','class'=>'form-control','id'=>'role']) ?>

									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Division</label>

									<?= Html::dropDownList('division', "$selected_division",ArrayHelper::map(Division::find()->where(['company_id'=>$selected_company])->orderBy('title')->all(), 'division_id', 'title'),['prompt'=>'--Select--','class'=>'form-control','id'=>'division']) ?>

									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Location</label>

									<?= Html::dropDownList('location', "$selected_location",ArrayHelper::map($location, 'location_id', 'name'),['prompt'=>'--Select--','class'=>'form-control','id'=>'location']) ?>

									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">State</label>

									<?= Html::dropDownList('state', "$selected_state",ArrayHelper::map(State::find()->where(['company_id'=>$selected_company])->orderBy('name')->all(), 'state_id', 'name'),['prompt'=>'--Select--','class'=>'form-control','id'=>'state']) ?>

									<div class="help-block"></div>
								</div>
							</div>
						</div>
				
						<input type="hidden" class="form-control" name="page" id="page" value="<?= $selected_page ?>">
						<div class="form-group">
							<button type="submit" id="submit_check"  class="btn btn-primary">Search</button>  
							<a class="btn btn-danger" href="<?php echo Url::to(['report/search'])?>" >Clear Search </a>
						</div>
					</form>
				</div>
			</div><!--end .card-body -->
		</div><!--end .card -->
		
<?php 

	
	if(isset($programs) && !empty($programs)){		
		foreach($programs as  $key=>$tmp)
		{				
			$overallprec = 0;
			$program_id = $tmp->program_id;
		 	$program_prec = $tmp->getAllEnrolledUserProgram($selected_company,$program_id,$firstname,$lastname,$selected_role,$selected_division,$selected_location,$selected_state);	
			if($program_prec !== false)
			{				
			$overallprec = $program_prec;					
			echo '<div class="col-md-3 pie-chart-align" ><div class="for-height" style="height:70px !important;"><label>'.$tmp->title.'</label></div><div data-id="'.$tmp->program_id.'" id="demo-pie-'.$tmp->program_id.'" class="pie-title-center dataclick" data-percent="'.$overallprec.'"> <span class="pie-value"></span> </div></div>'; 
			}	
		}
	}	


?>


<script type="text/javascript">

        $(document).ready(function () {
			
		$('.card-head').on('click', function (e) {
			var card = $(e.currentTarget).closest('.card');
			materialadmin.AppCard.toggleCardCollapse(card);
		});
		
		<?php if(isset($programs) && !empty($programs)){
		foreach($programs as  $tmp)
			{
		?>	
            $('#demo-pie-<?= $tmp->program_id ?>').pieChart({
                barColor: '#68b828',
                trackColor: '#eee',
                lineCap: 'square',
                lineWidth: 14,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });
		<?php } } ?>	
		
			$(".dataclick").click(function(){
				var id = $(this).attr('data-id');
				$("#program").val(id);
				$("#formsubmit").submit();
			});
          });
	</script>


<style>
.pie-title-center {
  display: inline-block;
  position: relative;
  text-align: center;
}

.pie-value {
  color: #68B828;
  font-weight: bold;
  font-size: 20px;
  display: block;
  position: absolute;
  height: 40px;
  top: 50%;
  left: 0;
  right: 0;
  margin-top: -20px;
  line-height: 40px;
}
.dataclick{
	cursor:pointer;
}
.pie-chart-align {
	text-align: center;
}
.for-height {
    width: 250px;
}
.for-height label {
    font-weight: 600;
}
</style>	
	
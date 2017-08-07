<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use common\models\UserProfile;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reset Progress';
$this->params['breadcrumbs'][] = $this->title;
	$selected_user = isset($params['user_id'])?$params['user_id']:'';
	//$selected_program = isset($params['program'])?$params['program']:'';
	$firstname = isset($params['firstname'])?$params['firstname']:'';
	$lastname = isset($params['lastname'])?$params['lastname']:'';
	$selected_role = isset($params['role'])?$params['role']:'';
	$selected_division = isset($params['division'])?$params['division']:'';
	$selected_location = isset($params['location'])?$params['location']:'';
	$selected_state = isset($params['state'])?$params['state']:'';
	$u_id = isset($params['unit'])?$params['unit']:'';
	$m_id = isset($params['module'])?$params['module']:'';
	$p_id = isset($params['program'])?$params['program']:'';
?>
    <h1><?= Html::encode($this->title) ?></h1>

	<div class="card">

		<div class ="card-body">
		<?php if (Yii::$app->session->hasFlash('select_some')): ?>	
			<div class="alert alert-danger fade in alert-dismissable">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
				<?= Yii::$app->session->getFlash('select_some'); ?>
			</div>
		<?php endif; ?>
		<?php //echo $this->render('_search', ['model' => $searchModel]); ?>

		<div class="card card-collapse">
			<div class="card-head style-default">
				<div class="tools">
					<div class="btn-group">
						<a class="btn btn-icon-toggle btn-collapse" data-toggle="collapsed"><i class="fa fa-angle-down"></i></a>
					</div>
				</div>
				<header>Search the results</header>
			</div><!--end .card-head -->
			<div class="card-body" style="display:none">
				<div class="program-search">
					<form method="get">

						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label" for="searchreport-c_id">First Name</label>
									<input type="text" class="form-control" name="custom_search[firstname]" value="<?=$firstname?>">
									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label" for="searchreport-c_id">Last Name</label>
									<input type="text" class="form-control" name="custom_search[lastname]" value="<?=$lastname?>">
									<div class="help-block"></div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4" >
								<?=Html::dropDownList(
									'custom_search[program]',
									$p_id,
									ArrayHelper::map(common\models\Program::find()->where(['company_id' =>Yii::$app->user->identity->c_id])->orderBy('title')->all(),'program_id', 'title'),
									[
									 'id' => 'program_select', 
									 'class' => 'form-control',
									 'prompt' => '--Select Program--',
									 'onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl('course/report/get-modules?p_id=').'"+$(this).val(), function( data ) {
										$( "select#module_select" ).html( data ).change();
										
									});'
									]
								);?>
							</div>
							<div class="col-md-4" >
								<?=Html::dropDownList(
									'custom_search[module]',
									$m_id,
									ArrayHelper::map(common\models\Module::find()->where(['program_id' =>$p_id,'status'=>1])->orderBy('title')->all(),'module_id', 'title'),
									[
									 'id' => 'module_select',
									 'class' => 'form-control',
									 'prompt' => '--Select Module--',
									 'onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl('course/report/get-units?m_id=').'"+$(this).val(), function( data ) {
										$( "select#unit_select" ).html( data ).change();
										
									});'
									 ]
								);?>
							</div>
							<div class="col-md-4" >
								<?=Html::dropDownList(
									'custom_search[unit]',
									$u_id,
									ArrayHelper::map(common\models\Unit::find()->where(['module_id' =>$m_id,'status'=>1])->orderBy('title')->all(),'unit_id', 'title'),
									[
									 'id' => 'unit_select','prompt' => '--Select Lesson--', 'class' => 'form-control']
								);?>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Role</label>
									<?= Html::dropDownList('custom_search[role]', "$selected_role",ArrayHelper::map(common\models\Role::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->orderBy('title')->all(), 'role_id', 'title'),['prompt'=>'--Select--','class'=>'form-control']) ?>
									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Division</label>
									<?= Html::dropDownList('custom_search[division]', "$selected_division",ArrayHelper::map(common\models\Division::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->orderBy('title')->all(), 'division_id', 'title'),['prompt'=>'--Select--','class'=>'form-control']) ?>
									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">Location</label>
									<?= Html::dropDownList('custom_search[location]', "$selected_location",ArrayHelper::map(common\models\Location::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->orderBy('name')->all(), 'location_id', 'name'),['prompt'=>'--Select--','class'=>'form-control']) ?>
									<div class="help-block"></div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="searchreport-user_id">State</label>
									<?= Html::dropDownList('custom_search[state]', "$selected_state",ArrayHelper::map(common\models\State::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->orderBy('name')->all(), 'state_id', 'name'),['prompt'=>'--Select--','class'=>'form-control']) ?>
									<div class="help-block"></div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary">Search</button>  
							<a class="btn btn-danger" href="<?= Url::to(['report/reset-users'])?>" >Clear Search </a>
						</div>
					</form>
				</div>
			</div><!--end .card-body -->
		</div><!--end .card -->		
		<?=Html::beginForm(['report/reset-users'],'post');?>
		<?php    
		if($_GET && isset($_GET['page']))
			$page = $_GET['page'];
		else $page = '';
		?>
		
		<p>
		<div class="row">
			<div class="col-md-6">
				<?=Html::input('hidden', 'search_params', serialize($params), ['class' =>'form-control'])?>
				<?=Html::input('hidden', 'page',$page, ['class' =>'form-control'])?>
				<?=Html::dropDownList('reset_type','',['all'=>'All Selected (both Awareness & Capability)','aw'=>'Selected Awareness Only','cp'=>'Selected Capability Only'], ['class' =>'form-control'])?>
			</div>
			<div class="col-md-2">
				<?=Html::submitButton('Reset', ['class' => 'btn btn-info',]);?>
			</div>
		</div>
		<p>
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
		//	'filterModel' => $searchModel,
		//	'layout' => '{items}{pager}',
			'columns' => [
				['class' => 'yii\grid\CheckboxColumn'],
				[
					'label'	=> 'Program',
					'value' => 'unit.module.program.title',
				],
				[
					'label'	=> 'Module',
					'value' => 'unit.module.title',
				],
				[
					'label'	=> 'Lesson',
					'value' => 'unit.title',
				],
				[
					'attribute' => 'student_id',
					'value' => 'student.fullname',
					'filter' => Html::activeDropDownList($searchModel, 'student_id', ArrayHelper::map(UserProfile::find()->joinWith(['user'])->where(['user.c_id'=>\Yii::$app->user->identity->c_id])->asArray()->all(), 'user_id', function($model, $defaultValue) {
							return $model['firstname'].'-'.$model['lastname'];
						}),['class'=>'form-control input-sm','prompt' => '--Search--']),
				],
				[
					'attribute' => 'Progress(Awareness/Capability)',
					'format' => 'raw',
					'value' => function($data) use($params){
						$user = common\models\User::findOne($data->student_id);
						$progress = $user->getUnitProgress($data->unit_id);
						$url = Url::to(['report/reset-test','type'=>'aw','r_id'=>$data->report_id,'params'=>serialize($params)]);
						$output = "<div name='unit1'>
									<a class='circle circle-{$progress['ap']}' href='$url'><span class='toolkit'>{$progress['ap']}</span>
									</a>
								";
						if($progress['cp'] == 'grey')
							$url = "javascript::void(0)";
						else $url = Url::to(['report/reset-test','type'=>'cp','r_id'=>$data->report_id,'params'=>serialize($params)]);
						$output .= "
									<a class='circle circle-{$progress['cp']}' href='$url'><span class='toolkit'>{$progress['cp']}</span>
									</a>
								</div>";								
						return $output;

					},
					 'contentOptions' => ['style' => 'text-align:center; width:250px;  min-width:100px;  '],
				],
			],
		]); ?>
		<?= Html::endForm();?> 
		</div>
	</div>
	<script>
//	$('.card-head .tools .btn-collapse').on('click', function (e) {
	$('.card-head').on('click', function (e) {
		var card = $(e.currentTarget).closest('.card');
		materialadmin.AppCard.toggleCardCollapse(card);
	});
	
/*	$( "#module_select" ).change(function() {					
		var mod_id = $(this).val();				
		window.location.href = "<?=Yii::$app->homeUrl;?>course/report/reset-users?type=m&u_id="+mod_id;
	});
	$( "#unit_select" ).change(function() {					
		var mod_id = $(this).val();				
		window.location.href = "<?=Yii::$app->homeUrl;?>course/report/reset-users?type=unit&u_id="+mod_id;
	}); */
	</script>

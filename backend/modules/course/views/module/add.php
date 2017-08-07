<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Company;
use common\models\Program;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\Module */
/* @var $form yii\widgets\ActiveForm */
//$this->registerCssFile(\Yii::$app->homeUrl."css/theme-default/libs/summernote/summernote.css?1425218701");

$this->registerJsFile(\Yii::$app->homeUrl."js/custom/waitingfor.js");
?>
<style>
	.checkbox.checkbox-styled>label>span:first-of-type {
		display: none;
	}
</style>
<div class="section-body contain-lg">
	<div class="row">
		<div class="col-lg-12">
			<div class="col-lg-10">
				<h1><?=$model->isNewRecord ?"Add Module":"Update: ".$model->title;?></h1>
				<?php if($program){ ?>
					<h4>[ Program: <a href="<?=Url::to(['program/view','id'=>$model->isNewRecord ?$program->program_id:$model->program->program_id])?>" ><?= $model->isNewRecord ?$program->title:$model->program->title;?> ]</a></h4>
				<?php } ?>
				
			</div>
			<div class="col-lg-2">
				<h1><?php
				if(!$model->isNewRecord )
				echo Html::a('Add Lesson', ['unit/create','m_id'=>$model->module_id], ['class' => 'btn btn-info pull-right']) ?>
			</h1>
			</div>
		</div><!--end .col -->
		<div class="col-lg-12">
			<div class="panel-group" id="accordion7">
				<div class="card panel">
					<div class="card-head style-primary " data-toggle="collapse" data-parent="#accordion7" data-target="#accordion7-1">
						<header>Step 1 - Module Overview</header>
						<div class="tools">
							<a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
						</div>
					</div>
					<?php if($model->isNewRecord ) {?>
					<div id="accordion7-1" class="collapse in">
					<?php } else {?>
					<div id="accordion7-1" class="collapse">
					<?php } ?>
						<div class="card-body">
							<?php $form = ActiveForm::begin([
											'options' => ['enctype'=>'multipart/form-data']
							]); ?>
								<h4>Module Status</h4>
								<?= $form->field($model, 'status',['options'=>['class'=>'form-group']])->checkbox(['class'=>'form-control'])->label(false) ?>
							
								<?= $form->field($model, 'title')->textInput(['maxlength' => true,'class'=>'form-control']) ?>
								
								
								<?= $form->field($model, 'short_description')->textarea(['id'=>'course_shortdesc']) ?>
								
								<h4>Listing Image</h4>
								<p><?php if(!$model->isNewRecord && $model->featured_image != ''){ ?>
									<img src="<?=Yii::$app->homeUrl.$model->featured_image?>" width="150px" height="150px"/>
								<?php } 
								else echo 'The image is used on the "Modules" listing ( archive ) page along with the module excerpt.';
								?></p>
								<?= $form->field($model, 'featured_image')->fileInput(['class'=>'form-control'])->label(false) ?>
								

	<?php if(Yii::$app->user->can("superadmin")){  ?>
									
								<h4>Company</h4>
								<?php 
							
									 $data = ArrayHelper::map(Company::find()->all(), 'company_id', 'name');	
									
									echo Select2::widget([
									   'name' => 'companyid',
									   'id' => 'companyid',
									   'value' => !empty($program->company_id)?$program->company_id:"", // value to initialize
									   'data' => $data,
									   'options' => [
											'placeholder' => 'Select Company',
											'disabled'=>$disabled,
											'onchange' => '$.post( "'.Yii::$app->urlManager->createUrl(		'course/program/get-program?c_id=').'"+$(this).val(), function( data ) {
													$( "select#module-program_id" ).html( data ).change();
														});
											
											']
									]);
									
								?>	
								
								<?php } ?>

								<h4>Program</h4>
								<?php 
								if(Yii::$app->user->can("superadmin"))
								{
									if(isset($program->company_id) && !empty($program->company_id))
										$data = ArrayHelper::map(Program::find()->where(['company_id'=>$program->company_id])->all(), 'program_id', 'title');
									else
										$data = ArrayHelper::map(Program::find()->all(), 'program_id', 'title');
								}
								else
									$data = ArrayHelper::map(Program::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->all(), 'program_id', 'title');
								echo $form->field($model, 'program_id')->widget(Select2::classname(), [
									'data' => $data,
									'options' => ['placeholder' => 'Select Program','disabled'=>$disabled],
								])->label(false);	
								?>	
								<?= $form->field($model, 'language')->textInput(['maxlength' => true,'class'=>'form-control']) ?>								
								<div class="form-group">
									<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-sm ink-reaction btn-default' : 'btn btn-sm ink-reaction btn-default']) ?>
								</div>
							<?php ActiveForm::end(); ?>
						</div>
					</div>
				</div><!--end .panel -->
				</br>
				<div class="card panel">
					<div class="card-head collapsed" data-toggle="collapse" data-parent="#accordion7" data-target="#accordion7-2">
						<header>Step 2 - Module Description</header>
						<div class="tools">
							<a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
						</div>
					</div>
					<?php if(!$model->isNewRecord){ ?>
						<div id="accordion7-2" class="collapse">
							<div class="card-body">
							<?php $form = ActiveForm::begin(); ?>
								
									<h4>Featured Video</h4>
									<p><?php if(!$model->isNewRecord && $model->featured_video_url != ''){ 
												$url = $model->featured_video_url; 
										
									if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
											echo "<iframe width='600' height='300' src=".$url." controls></iframe>";
									} else {
											echo $url;
									}
									}
									else echo 'This is used on the module Overview page and will be displayed with the module description.';
									?></p>
									<div class="col-md-5">
									<?= $form->field($model, 'featured_video_url')->textInput(['class'=>'form-control','onChange'=>'saveVideoUrl(this)','id'=>'video_url'])->label(false) ?>
									</div>
									<div class="col-md-1"> ( Or ) </div> 
									<div class="col-md-6">
									<?= $form->field($model, 'featured_video_upload')->fileInput(['onChange'=>'saveFile(this)','class'=>'form-control'])->label(false) ?>
									</div>

									<h4>Module Description	</h4>
									<p>This is an in-depth description of the module. It should include such things like an overview, outcomes, possible requirements, etc.</p>

									<?= $form->field($model, 'detailed_description')->textarea(['id'=>'course_desc']) ?>
								
								<div class="form-group">
									<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-sm ink-reaction btn-default' : 'btn btn-sm ink-reaction btn-default','id' => $model->isNewRecord ? '':'update_check']) ?>
								</div>
							<?php ActiveForm::end(); ?>
							</div>
						</div>						
					<?php } ?>

				</div><!--end .panel -->
				</br>
				<div class="card panel">
					<div class="card-head collapsed" data-toggle="collapse" data-parent="#accordion7" data-target="#accordion7-3">
						<header>Step 3 - Module Dates</header>
						<div class="tools">
							<a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
						</div>
					</div>
					<?php if(!$model->isNewRecord){ ?>
					<div id="accordion7-3" class="collapse">
						<div class="card-body">
						<?php $form = ActiveForm::begin(); ?>
							<div class="form-group">
								<h4>Module Dates</h4>
								<?= $form->field($model, 'is_course_open_anytime',['template' => '<div class="checkbox checkbox-styled"><label>{input}<span>This module has no end date</span></label></div>','options'=>['tag'=>'div']])->checkbox([],false); ?>
								<p>This is the duration the module will be open to the students</p>
							</div>
							<div class="form-group">
								<div class="input-daterange input-group" id="start_date">
										<?= $form->field($model, 'course_start_date')->textInput(['template'=>'<div class="input-group-content">{input}</div>','class'=>'form-control']) ?>
										<span class="input-group-addon">to</span>
										<?= $form->field($model, 'course_end_date')->textInput(['template'=>'<div class="input-group-content">{input}</div>','class'=>'form-control']) ?>
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								</div>
							</div><!--end .form-group -->
							<div class="form-group">
								<h4>Enrollment Dates</h4>
										<?= $form->field($model, 'is_enrlmnt_open_anytime',['template' => '<div class="checkbox checkbox-styled"><label>{input}<span>Users can enroll at any time</span></label></div>','options'=>['tag'=>'div']])->checkbox([],false); ?>

								<p>These are the dates that students can enroll</p>
							</div>
							<div class="form-group">
								<div class="input-daterange input-group" id="start_enroll_date">
										<?= $form->field($model, 'enrl_start_date')->textInput(['template'=>'<div class="input-group-content">{input}</div>','class'=>'form-control']) ?>
										<span class="input-group-addon">to</span>
										<?= $form->field($model, 'enrl_end_date')->textInput(['template'=>'<div class="input-group-content">{input}</div>','class'=>'form-control']) ?>
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								</div>
							</div><!--end .form-group --></br>
							<div class="form-group">
								<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-sm ink-reaction btn-default' : 'btn btn-sm ink-reaction btn-default']) ?>
							</div>
						<?php ActiveForm::end(); ?>
						</div>
					</div>
					<?php } ?>
				</div><!--end .panel -->
			</div><!--end .panel-group -->
		</div><!--end .col -->
	</div><!--end .row -->
	<!-- END COLORS -->

</div><!--end .section-body -->
<?php 
//$this->registerJsFile(\Yii::$app->homeUrl."js/libs/summernote/summernote.min.js");
//$this->registerJsFile(\Yii::$app->homeUrl."js/libs/multi-select/jquery.multi-select.js");
//$this->registerJsFile(\Yii::$app->homeUrl."js/libs/bootstrap-datepicker/bootstrap-datepicker.js")
?>
<script>
$(document).ready(function(){
//$('#optcategory').multiSelect({selectableOptgroup: true});
$('#course_desc').summernote();
$('#course_shortdesc').summernote();
$('#start_date').datepicker({
	autoclose: true, 
	todayHighlight: true,
	format: 'yyyy-mm-dd'
});
$('#start_enroll_date').datepicker({autoclose: true, todayHighlight: true,format: 'yyyy-mm-dd'});
	//handle the date fields
	if($('#module-is_course_open_anytime').is(":checked")){
		$("#module-course_start_date").prop('disabled', true);
		$("#module-course_end_date").prop('disabled', true);
		console.log("checked");
	}
	if($('#module-is_enrlmnt_open_anytime').is(":checked")){
		$("#module-enrl_start_date").prop('disabled', true);
		$("#module-enrl_end_date").prop('disabled', true);
		console.log("checked");
	}
});

$('#module-is_course_open_anytime').on('click',function(){
	if($(this).is(":checked")){
		$("#module-course_start_date").prop('disabled', true);
		$("#module-course_end_date").prop('disabled', true);
		console.log("checked");
	}else{
		$("#module-course_start_date").prop('disabled', false);
		$("#module-course_end_date").prop('disabled', false);		
	}
	
});
$('#module-is_enrlmnt_open_anytime').on('click',function(){
	if($(this).is(":checked")){
		$("#module-enrl_start_date").prop('disabled', true);
		$("#module-enrl_end_date").prop('disabled', true);
		console.log("checked");
	}else{
		$("#module-enrl_start_date").prop('disabled', false);
		$("#module-enrl_end_date").prop('disabled', false);		
	}
	
});


<!---------- Save file -------------------->
function saveFile(input){

 	var ext = input.files[0]['name'].substring(input.files[0]['name'].lastIndexOf('.') + 1).toLowerCase();
	file = input.files[0];
	var ext = input.files[0]['name'].substring(input.files[0]['name'].lastIndexOf('.') + 1).toLowerCase();	
 	if(file != undefined){
		
		waitingDialog.show('Uploading..');
	 formData= new FormData();
	if(ext == "mp4" || ext == "m4v" || ext == "webm" || ext == "ogv"){
		formData.append("media", file); 
		$.ajax({
			url: "<?=Url::to(['module/upload'])?>",
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function(data){
				waitingDialog.hide();
				//$(input).attr('src', data);
				$('#video_url').val(data);
			}
		}); 
 	}else{
		alert("Extension not supported");
		waitingDialog.hide();
		//$(input).val("");
		return false;
	} 


	} else {
		alert("file Input Error");
	}
}
//($('.fld-description').val()).length;
<!---------- End of save file ------------->
function saveVideoUrl(input){
	$("#update_check").attr("disabled","disabled");
	console.log("tbp",$(input).val());
	var url = $(input).val();
	if(url!=''){
	waitingDialog.show('Fetching..');
		$.ajax({
			url: "<?=Url::to(['unit/embed'])?>?url="+url,
			type: "GET",
			processData: false,
			contentType: false,
			success: function(data){
				waitingDialog.hide();
				$('#video_url').val(data);
			},
			error:function(data){
				alert("Oops!Something wrong happend. Please try again later");
				waitingDialog.hide();
			}
		});		
	}
	$("#update_check").removeAttr("disabled");
	//$(input).prev().attr('src',$(input).val());
	//console.log('src',$(input).prev().attr('src'));
}




</script>
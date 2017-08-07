<?php 

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\UnitElement;

$this->registerCssFile(\Yii::$app->homeUrl."css/custom/form-builder.css");
$this->registerCssFile(\Yii::$app->homeUrl."css/custom/form-render.css");
$this->registerJsFile(\Yii::$app->homeUrl."js/custom/form-builder.js");
$this->registerJsFile(\Yii::$app->homeUrl."js/custom/form-render.js");
$this->registerJsFile(\Yii::$app->homeUrl."js/custom/waitingfor.js");
$this->registerJsFile(\Yii::$app->homeUrl."js/custom/jquery-ui.min.js");

?>
<style>
button#frmb-0-view-data,button#frmb-4-view-data,button#frmb-2-view-data{
	display:none;
}
.required-wrap{
	display:none;
}
.prev_video{
	display:none;
}
</style>
<div class="section-body contain-lg">
<h2 class="col-md-9">Update Lesson: <?=$model->title?></h2>

<div class="row">

<div class="small-padding"><a class="btn btn-info pull-right" href="<?=Url::to(['unit/preview','u_id'=>$model->unit_id])?>">Preview Lesson</a></div>

	<div class="col-lg-12">
	<h4 class="small-padding">[ Program: <a href="<?= Url::to(['program/view','id'=>$module->program->program_id])?>"><?=$module->program->title?></a> , Module: <a href="<?= Url::to(['module/update','id'=>$module->module_id])?>"><?=$module->title?> ]</a></h4>
	<div class="card tabs-left style-default-light">
		<ul id="sortable" class="card-head nav nav-tabs tabs-info" data-toggle="tabs">
			<?php foreach($module->units as $unit){
				if($unit->unit_id == $model->unit_id)
					echo '<li id="unit_'.$unit->unit_id.'" class="ui-state-default active"><a href="#tab1">'.substr($unit->title,0,12).'..</a></li>';
				else
					echo '<li id="unit_'.$unit->unit_id.'" class="ui-state-default"><a class="unit_view" data-unit_id="'.$unit->unit_id.'" href="#tab2">'.substr($unit->title,0,12).'..</a></li>';
			}?>
			<li class="ui-state-disabled"><a class="unit_view" data-unit_id="new" href="#tab2">Add New Lesson</a></li>
		</ul>
		<div class="card-body tab-content style-default-bright">
		<div class="tab-pane active" id="tab1">
			<div class="panel-group" id="unit_accordian">
				<div class="card panel expanded">
					<div class="card-head style-info" data-toggle="collapse" data-parent="#unit_accordian" data-target="#unit_details" aria-expanded="true">
						<header>Lesson Details</header>
						<div class="tools">
							<a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
						</div>
					</div>
					<div id="unit_details" class="collapse" aria-expanded="true">
						<div class="card-body">
							<a class="btn btn-success" href="<?=Url::to(['unit/preview','u_id'=>$model->unit_id])?>">Preview</a> 
							<div class="checkbox checkbox-styled checkbox-info  pull-right">
								<label>
								<?php 
									if($model->status )
										$checked = "checked";
									else $checked = "";
								?>
									<input id="unit_status" type="checkbox" <?=$checked?>>
									<span>Publish</span>
								</label>
							</div>
							
							<div class="form-group field-unit-title required">
								<label>Lesson Title</label>
								<input type="text" id="unit_title" class="form-control" value="<?=$model->title ?>">
								<div class="help-block"></div>
								
							</div>
							<div class="form-group field-reset-period">
								<label>Auto Reset Period [In months]</label>
								<input type="text" id="auto_reset_period" class="form-control" value="<?=$model->auto_reset_period?>">
								<div class="help-block"></div>
								
							</div>
							<div class="form-group">
								<div class="checkbox checkbox-styled">
									<label>
										<?php 
											if($model->show_learning_page )
												$checked = "checked";
											else $checked = "";
										?>
										<input id="show_learning_page" type="checkbox" <?=$checked?>>
										<span>Show Lesson Details Page</span>
									</label>
								</div>
							</div>
							<?php 
								$element = UnitElement::find()->where(['unit_id'=>$model->unit_id,'element_type'=>'page'])->one();
								$data = json_decode($element->content);
								$formdata = $data->html;
								$formdata = str_replace("'", "\'", $formdata);
								$formdata = str_replace(array("\r", "\n"), '', $formdata);
								//echo $formdata;die;
							?>
							<div id="build-wrap"></div>
							<!--<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Preview Lesson</button>-->
							<a class="btn btn-success" href="<?=Url::to(['unit/preview','u_id'=>$model->unit_id])?>">Preview</a> 
						</div>
					</div>
				</div><!--end .panel -->
				<br>
				<div class="card panel">
					<div class="card-head style-info collapsed" data-toggle="collapse" data-parent="#unit_accordian" data-target="#awareness_test" aria-expanded="false">
						<header>Awareness Test</header>
						<div class="tools">
							<a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
						</div>
					</div>
					<div id="awareness_test" class="collapse" aria-expanded="false">
						<div class="card-body">
						<a class="btn btn-success" href="<?=Url::to(['unit/preview','u_id'=>$model->unit_id])?>#aw_test">Preview</a> 
							<?php 
								$element = UnitElement::find()->where(['unit_id'=>$model->unit_id,'element_type'=>'aw_data'])->one();
								$aw_data = $element->content;
							
						//$aw_data = "<form-template><fields><field type='checkbox-group' description='' label='Multiple &amp;amp;amp; Choice' class='checkbox-group' name='checkbox-group-938' src='false'><option option_id='2430' label='option-1         ' value='option-1         ' selected='true'>'option-1        '</option><option option_id='2431'  label='option-2         ' value='option-2         '>'option-2         '</option><option option_id='2432'  label='option-3         ' value='option-3         '>'option-3         '</option></field></fields></form-template>";
							
								//$aw_data = str_replace("&amp;", "&", $aw_data);
								//$aw_data = html_entity_decode($aw_data);								
								//var_dump($aw_data);exit;
								
								//print_r($aw_data);die;
								
								$aw_data = str_replace("&amp;", "&", $aw_data);								
								$aw_data = str_replace("'", "\'", $aw_data);
								//echo $aw_data;exit;
								
								//$aw_data = str_replace('"', '&quot;', $aw_data);
							?>
						<div id="aware_form"></div>
						<a class="btn btn-success" href="<?=Url::to(['unit/preview','u_id'=>$model->unit_id])?>#aw_test">Preview</a> 
						</div>
					</div>
				</div><!--end .panel -->
				<br>
				<div class="card panel">
					<div class="card-head style-info collapsed" data-toggle="collapse" data-parent="#unit_accordian" data-target="#capability-test" aria-expanded="false">
						<header>Capability Test</header>
						<div class="tools">
							<a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
						</div>
					</div>
					<div id="capability-test" class="collapse" aria-expanded="false">
						<div class="card-body">
							<?php 
								$element = UnitElement::find()->where(['unit_id'=>$model->unit_id,'element_type'=>'cap_data'])->one();
								$cp_data = $element->content;
								$cp_data = html_entity_decode($cp_data);
								$cp_data = str_replace("'", "\'", $cp_data);
								//$cp_data = str_replace(array("\r", "\n"), '', $cp_data);
							?>
						<div id="capability_form"></div>
						</div>
					</div>
				</div><!--end .panel -->
			</div><!--end .panel-group -->
		</div>
		</div>
	</div>
	</div><!--end .col -->
</div><!--end .row -->
<!-- END COLORS -->
</div>
<!-- Bootstrap modal dialog -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Preview Lesson</h4>
            </div>
            <div class="modal-body">
                <?php echo $this->render('preview_lesson', ['model'=>$model]); ?>              
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
  $( function() {
    $( "#sortable" ).sortable({
		items: "li:not(.ui-state-disabled)",
		cancel: ".ui-state-disabled",
		axis: 'y',
		update: function (event, ui) {
			var data = $(this).sortable('serialize');
			//console.log('data',data);
			// POST to server using $.post or $.ajax
			 $.ajax({
				data: data,
				type: 'POST',
				url: '<?=Url::to(['unit/sort'])?>'
			}); 
		}
	});
    $( "#sortable" ).disableSelection();
  } );
$(document).ready(function(){
	
	<!---------- validate unit title ------------>
	$('.unit_view').click(function(){
		//window.location.replace("<?=Url::to(['unit/update'])?>?id="+$(this).attr('data-unit_id'));
		if($(this).attr('data-unit_id') == 'new')
			var url = "<?=Url::to(['unit/create','m_id'=>$model->module_id])?>";
		else var url = "<?=Url::to(['unit/update'])?>?id="+$(this).attr('data-unit_id');
		$(location).attr('href',url);
	});
	<!---------- validate unit title ------------>
	<!---------- validate unit title ------------>
	$('#unit_title').on("blur",function(){
		var unit_title = $(this).val();
		if(!unit_title || unit_title == ''){
			$('.field-unit-title .help-block').html('Title cannot be blank');
			$('.field-unit-title').addClass('has-error');
			$('.field-unit-title').removeClass('has-success');
		}else{
			$('.field-unit-title .help-block').html('');
			$('.field-unit-title').addClass('has-success');	
			$('.field-unit-title').removeClass('has-error');			
		}
	});
	<!---------- validate unit title ------------>
	
	<!--------------unit elements-------------->
	var unit_element_options = {
		disableFields: ['autocomplete','button','checkbox','textarea','checkbox-group','hidden','select','header','date','number','radio-group','paragraph','text','fileupload'],
		fieldRemoveWarn: true,
		controlPosition: 'left',
	};
	var unit_element_editor = $(document.getElementById('build-wrap'));
 	var formData = '<?= $formdata ?>';
	//formData.replace(/"/g,"\\\"");
	//console.log(formData);
	if (formData) {
		unit_element_options.formData = formData;
	}	 
	$(unit_element_editor).formBuilder(unit_element_options);
	var saveBtn = document.querySelector('#frmb-0-save');
	saveBtn.onclick = function() {
		
		var unit_status = 0;
		if($('#unit_status').is(":checked"))
			unit_status = 1;
		var show_learning_page = 0;
		if($('#show_learning_page').is(":checked"))
			show_learning_page = 1;		
		
		var auto_reset_period = $('#auto_reset_period').val();
		if(auto_reset_period.length > 2){
			$('.field-reset-period').addClass('has-error');
			$('.field-reset-period .help-block').html('Invalid reset period');
			return false;
		}
		var unit_title = $('#unit_title').val();
		if(!unit_title || unit_title == ''){
			$('.field-unit-title .help-block').html('Title cannot be blank');
			$('.field-unit-title').addClass('has-error');
			return false;
		}
		//see if any of the url fields are empty
		var req = [];
		$('.url_field').each(function(){
			if($(this).val() =='')
			{
				alert("Please upload the "+$(this).attr("data_mc_type")+" file/s");
				req.push($(this).attr("data_mc_type"));
				//return false;
			}
		});
		if(req.length > 0){
			return false;
		}
			
		//console.log($(unit_element_editor).data('formBuilder').formData);
		var builder_data = JSON.stringify({'html':$(unit_element_editor).data('formBuilder').formData});
		//save to db
		$.ajax({
			url:'<?=Url::to(['unit/update','id'=>$model->unit_id])?>',
			data: {unit_title:unit_title,builder_data : builder_data,unit_status:unit_status,reset_period:auto_reset_period,show_learning_page:show_learning_page},
			type: 'post',
			dataType : 'json',
			success : function(data){
				console.log(data);
			}
		});
		//console.log($(unit_element_editor).data('formBuilder').formData);
		//window.sessionStorage.setItem('formData', $(unit_element_editor).data('formBuilder').formData);
	};
	<!----------end of unit elements----------->
	<!---------- start of awareness elements ------->
	var awareness_elements = {
		disableFields: ['autocomplete','button','checkbox','textarea','hidden','header','date','number','select','paragraph','textdisplay','video','audio',],
	  //disableFields: ['autocomplete','button','checkbox','textarea','hidden','header','date','number','select','img','video','audio','paragraph','textdisplay','filedownload'],
	   controlOrder: [
        'checkbox-group',
        'radio-group',
		'text',
		'fileupload',
		'img',
		'filedownload',
		],
	   fieldRemoveWarn: true ,
	   controlPosition: 'left',
	   editOnAdd: true,
	};
 	var aw_data = '<?=$aw_data?>';
	
	//aw_data.replace(/"/g,"&quot;");
	//var aw_data = aw_data.replace(/&amp;/g, '&');
	//console.log(aw_data);
	if (aw_data) {
		awareness_elements.formData = aw_data;
	}	
	
	var awareness_editor = $(document.getElementById('aware_form'));
	
	$(awareness_editor).formBuilder(awareness_elements);
	
	
	//$('#aware_form').formBuilder(awareness_elements);
	//SaveAwarenessTest
	
	var saveBtn = document.querySelector('#frmb-2-save');
	saveBtn.onclick = function() {
		var req = [];
		$('.url_field').each(function(){
			if($(this).val() =='')
			{
				alert("Please upload the "+$(this).attr("data_mc_type")+" file/s");
				req.push($(this).attr("data_mc_type"));
				//return false;
			}
		});
		if(req.length > 0){
			return false;
		}
		var form_data = $(awareness_editor).data('formBuilder').formData;
		// Arivu Check		
		//alert(JSON.stringify(form_data));return;
		//var awareness_data = JSON.stringify({'html':form_data});
		var awareness_data = JSON.stringify({'html':form_data});
	

		//save to db
		$.ajax({
			url:'<?=Url::to(['unit/save-test','type'=>'aw'])?>',
			data: {unit_id:'<?=$model->unit_id?>',data : awareness_data},
			type: 'post',
			dataType : 'json',
			success : function(data){
				//console.log(data);
			}
		});	
	}
	<!---------- end of awareness elements ------->
	var cap_elements = {
	  disableFields: ['autocomplete','button','checkbox','textarea','checkbox-group','hidden','select','header','date','number','file','paragraph','text','img','video','audio','textdisplay','fileupload','filedownload'],
	   fieldRemoveWarn: true,
	   controlPosition: 'left',
	   editOnAdd: true,
	};
 	var cp_data = '<?= $cp_data ?>';
	//console.log(aw_data);
	if (aw_data) {
		cap_elements.formData = cp_data;
	}	
	var cap_editor = document.getElementById('capability_form');
	$(cap_editor).formBuilder(cap_elements);
	var saveBtn = document.querySelector('#frmb-4-save');
	saveBtn.onclick = function() {
		var form_data = $(cap_editor).data('formBuilder').formData;
		var cap_data = JSON.stringify({'html':form_data});
		//save to db
		$.ajax({
			url:'<?=Url::to(['unit/save-test','type'=>'cp'])?>',
			data: {unit_id:'<?=$model->unit_id?>',data : cap_data},
			type: 'post',
			dataType : 'json',
			success : function(data){
				//console.log(data);
			}
		});	
	}
});
<!---------- Save file -------------------->
function saveFile(input){
	//console.log("mc_type",$(input).attr('data_mc_type'));
	var mc_type = $(input).attr('data_mc_type');
	supportedFormats = [];
		if(mc_type == 'video')
			supportedFormats = ['mp4','m4v','webm','ogv'];
		if(mc_type == 'audio')
			supportedFormats = ['mp3','ogg','wma','m4a','wav'];
		if(mc_type == 'image'){
			supportedFormats = ['jpg','jpeg','jpe','gif','png','bmp','tif','tiff','ico'];	
		}		
		if(mc_type == 'file')
			supportedFormats = ['pdf','doc','docx','ppt','pptx'];
	
	var ext = input.files[0]['name'].substring(input.files[0]['name'].lastIndexOf('.') + 1).toLowerCase();
	file = input.files[0];

	if (0 > supportedFormats.indexOf(ext)) {
		alert("Extension not supported");
		//clear all
		$(input).next().val("");
		$(input).val("");
		return false;
	}
	if(file != undefined){
		waitingDialog.show('Uploading..');
		formData= new FormData();
		formData.append("media", file);
		$.ajax({
			url: "<?=Url::to(['unit/upload'])?>",
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function(data){
				waitingDialog.hide();
				$(input).attr('src', data);
				$(input).attr('data_media_type', ext);
				$(input).next().val(data);
			},
			error:function(data){
				alert("Oops!Something wrong happend. Please try again later");
				waitingDialog.hide();
			}
		});
	}
}
function saveUrl(input){
	//console.log("tbp",$(input).val());
	var url = $(input).val();
	if(url !=''){
		var ext = url.substring(url.lastIndexOf(".")+1);
		var mc_type = $(input).attr('data_mc_type');	
		supportedFormats = [];
			if(mc_type == 'audio')
				supportedFormats = ['mp3','ogg','wma','m4a','wav'];
			if(mc_type == 'image'){
				supportedFormats = ['jpg','jpeg','jpe','gif','png','bmp','tif','tiff','ico'];	
			}		
			if(mc_type == 'file')
				supportedFormats = ['pdf','doc','docx','ppt','pptx'];
			if (0 > supportedFormats.indexOf(ext)) {
				alert("Invalid Url");
				//clear all
				$(input).val("");
				return false;
			}		
		$(input).prev().attr('src',$(input).val());		
	}
}

function IsValidImageUrl(url) {
$("<img>", {
    src: url,
    error: function() { alert(url + ': ' + false); },
    load: function() { alert(url + ': ' + true); }
});
}

function saveVideoUrl(input){
	//console.log("tbp",$(input).val());
	var url = $(input).val();
	if(url!=''){
		$('#frmb-0-save').attr("disabled",true);
		waitingDialog.show('Fetching..');
			$.ajax({
				url: "<?=Url::to(['unit/embed'])?>?url="+url,
				type: "GET",
				processData: false,
				contentType: false,
				success: function(data){
					waitingDialog.hide();
					$(input).prev().attr('src',data);
					$('#frmb-0-save').attr("disabled",false);
				},
				error:function(data){
					alert("Oops!Something wrong happend. Please try again later");
					waitingDialog.hide();
					$('#frmb-0-save').attr("disabled",false);
					$(input).val("");
				}
			});
	}
	//$(input).prev().attr('src',$(input).val());
	//console.log('src',$(input).prev().attr('src'));
}
//$('.fld-description').summernote();
//$('.fld-label').val('');
//($('.fld-description').val()).length;
<!---------- End of save file ------------->
</script>
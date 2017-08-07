<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model common\models\Module */

$this->title = "Copy Module";
$this->params['breadcrumbs'][] = ['label' => 'Modules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-view">

    <h1><?= Html::encode($this->title) ?></h1>

		<?php 
$sessioncheck = Yii::$app->session->getFlash('Success');
if(isset($sessioncheck) && !empty($sessioncheck)) { ?>
<div id="w3-success-0" class="alert-success alert fade in">
<button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
<?= Yii::$app->session->getFlash('Success'); ?>
</div>
<?php } ?>

	<div class="card">
	<div class="card-body">
	
			<form id="forum_post" action="<?=Url::to(['copy/index'])?>" method="POST" >
			<div class="row">
				<div class="col-md-6">								
				   <?php $data = ArrayHelper::map($program, 'program_id', 'title'); ?>	

				<div class="form-group field-copymodule-program_id ">
					<label class="control-label" for="copymodule-program_id">From Program</label>
					<select id="copymodule-program_id" class="form-control" name="CopyModule[program_id]">
					<option value="">--Select Program--</option>
						<?php 
							foreach($data as $key=>$tmp)
							{
								echo "<option value=".$key.">".$tmp."</option>";
							}	
						?>
					</select>
					<div class="help-block copymodule-program_id" style="display: none;">Program Name cannot be blank.</div>
				</div>
					
				</div>
				
				<div class="col-md-6">					
					<div class="form-group field-copymodule-copy_program">
						<label class="control-label" for="copymodule-copy_program">To Program</label>
						<select id="copymodule-copy_program" class="form-control" name="CopyModule[copy_program]">
							<option value="">--Select Program--</option>
							<?php 
							 foreach($data as $key=>$tmp)
							 {
								echo "<option value=".$key.">".$tmp."</option>";
							 }	
							?>
						</select>
						<div class="help-block copymodule-copy_program" style="display: none;">Copy To Program Name cannot be blank.</div>						
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">									
					<div class="form-group field-copymodule-copy_module">
						<label class="control-label" for="copymodule-copy_module">Module to Copy</label>
						<select id="copymodule-copy_module" class="form-control" name="CopyModule[copy_module]">
							<option value="">--Select Module--</option>
						</select>
						<div class="help-block copymodule-copy_module" style="display: none;">Module Copy cannot be blank</div>
					</div>

					
				</div>
			</div>
				
		<div class="form-group">
			<?= Html::submitButton('Copy', ['class' =>'btn btn-success','id'=>'clickcopy']) ?>
		</div>
		
		</form>
	</div>
</div>

</div>


<script type="text/javascript">
$(document).ready(function(){	
 	$( "#forum_post" ).submit(function() {
		var test = 0;
		$(".form-control").each(function(){
			var id = $(this).attr('id');
			var val = $(this).val();			
			if($.trim(val) == "")
			{	
				$("."+id).show();
				$(".field-"+id).addClass("has-error");
				test = 1;
			}							
		});
		if($.trim(test) == 1)			
			return false;
		
		var program_id = $("#copymodule-program_id").val();
		var copy_program_id = $("#copymodule-copy_program").val();
		if($.trim(program_id) == $.trim(copy_program_id))
		{
			alert("both program is same");
			return false;
		}
		$("#clickcopy").attr("disabled","disabled");
	}); 

	
	 $(".form-control").change(function(){
		 var id = $(this).attr('id');
		 $("."+id).hide();
		 $(".field-"+id).removeClass("has-error");
		// $("#clickcopy").removeAttr("disabled");
	 });
	 
    $("#copymodule-program_id").change(function(){
		var program_id = $(this).val();
		 $.ajax({
				   url: '<?=Url::to(['copy/get-modules'])?>',
				   type: 'POST',
				   data: {  program_id: program_id,
				   },
				   success: function(data) {		
						$("#copymodule-copy_module").html(data);						
				   }
				 }); 		
    });

});
</script>


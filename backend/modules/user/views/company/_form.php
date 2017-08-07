<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\MyCaar;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="card">
	<div class="card-body">

    <?php $form = ActiveForm::begin( ['options' => ['enctype'=>'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'about_us')->textarea(['rows' => 3]) ?>
	
	<?php if($model->isNewRecord){ 
		  echo $form->field($model, 'logo')->fileInput(['class'=>'form-control']);
	 } else if($model->logo != '' ) { ?>	
		<div class="form-group field-company-logo" >
			<label class="control-label" >Logo</label>
				<img id='imagesorce' src="<?=Yii::$app->homeUrl.$model->logo ?>" height="100px" width="300px"/>
				<a id="<?= $model->company_id ?>" style="cursor:pointer" class="removelogo" >Change Logo</a>
		</div>	
	<?php 	
			
			echo $form->field($model, 'logo')->fileInput(['class'=>'form-control','style'=>'display:none'])->label(False);
		} else { 	
			echo $form->field($model, 'logo')->fileInput(['class'=>'form-control']);
	} ?>
		 
		 
	<?php
		  if(\Yii::$app->user->can('company manage')) 
		 { ?>	 
	 <a id="newuser" name="newuser" style="cursor:pointer" data-toggle="modal" data-target="#newuserreq" >[ New-User ]</a>	 
	 <?php
			  $data = ArrayHelper::map(MyCaar::getUserAllByrole("company_admin"), 'id', 'email');			
			 echo $form->field($model, 'admin')->widget(Select2::classname(), [
				 'data' => $data,
				 'options' => ['placeholder' => 'Select Company Admin'],
			 ]);	
			 
		 }  ?> 
			
		<?= $form->field($model, 'slug')->textInput(['maxlength' => 100]) ?>
		
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

	</div>
</div>



<!-- New User style pop up -->
	<!-- Modal -->
<!-- BEGIN FORM MODAL MARKUP -->

<div class="modal fade" id="newuserreq" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="z-index: 9999 !important;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="formModalLabel">Add New Company Admin User</h4>
			</div>
			<form class="form-horizontal" role="form" >
				<div class="modal-body">
				<div class="form-group">
						<div class="col-sm-12">
							<label for="email1" class="control-label">Firstname *</label>
						</div>
						<div class="col-sm-12">
							<input type="text" name="firstname" id="firstname" class="form-control" required >
						</div>
						<div style="color:red;margin-left:20px" class="error-msg error-firstname"> </div>
					</div>	
					<div class="form-group">
						<div class="col-sm-12">
							<label for="text" class="control-label">Lastname *</label>
						</div>
						<div class="col-sm-12">
							<input type="text" name="lastname" id="lastname" class="form-control" required >
						</div>
						<div style="color:red;margin-left:20px" class="error-msg error-lastname"> </div>
					</div>	
					<div class="form-group">
						<div class="col-sm-12">
							<label for="email1" class="control-label">Username / Email *</label>
						</div>
						<div class="col-sm-12">
							<input type="email" name="newemail_id" id="newemail_id" class="form-control" required placeholder="Email">
						</div>
						<div style="color:red;margin-left:20px" class="error-msg error-email"> </div>
					</div>						
				</div>
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>-->
					<button type="button" id="addnewuser" name="addnewuser" class="btn btn-primary">Add New User</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- END FORM MODAL MARKUP -->
<!-- New User style pop up -->

  <script type="text/javascript"> 
  $(function(){	
			$('#company-logo').change( function(event) {
				var tmppath = URL.createObjectURL(event.target.files[0]);
				$("#imagesorce").fadeIn("fast").attr('src',tmppath);
			});
  });  
  </script>
  
  
<script>
 $(document).ready(function () {
	function validateEmail($email) {
		var emailReg =  /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
		return emailReg.test( $email );
		}

		

$( "#updateform" ).submit(function( event ) {
  alert( "Handler for .submit() called." );  
});

 
 
 $("#newuser").click(function() { 
	 $("#firstname").val('');
	 $("#lastname").val('');
	 $("#newemail_id").val('');
	 $(".error-email").html("");
 });
 
	  $("#addnewuser").click(function() {			
			var firstname = $('#firstname').val();
			var lastname = $('#lastname').val();
			var useremail = $('#newemail_id').val();
			if($.trim(firstname) == "") { 
				$(".error-firstname").html("Please Fill Firstname field"); 
				return false;
			} else {
				$(".error-firstname").html("");
			}
			if($.trim(lastname) == "") { 
				$(".error-lastname").html("Please Fill Lastname field"); 
				return false;
			} else {
				$(".error-lastname").html("");
			}
			if( !validateEmail(useremail)) { 
				$(".error-email").html("Invalid email Address!!!."); 
				return false;
			} else {
				$(".error-email").html("");
			}
			$(this).attr("disabled","disabled");
				 $.ajax({
				   url: '<?=Yii::$app->homeUrl."user/company/ajax-new-user"?>',
				   type: 'POST',
				   data: {  
				   firstname: firstname,
				   lastname: lastname,
				   emailid: useremail,
				  
				   },
				   success: function(data) {												
						if($.trim(data) == 'false')
						{
							$(".error-email").html("This Email Id has already used!!!.");
							$("#addnewuser").removeAttr('disabled');
							return false;
						} else {
							$("#company-admin").append(data);
							$( ".close" ).trigger( "click" );
						}
				   }

				}); 			
		  });
/* 
		  $(".removelogo").click(function() {					  
			  var company_id = $(this).attr('id');
				
				$.ajax({
				   url: '<?=Yii::$app->homeUrl."user/company/removelogo"?>',
				   type: 'POST',
				   data: {  company_id:company_id
				   },
				   success: function(data) {
							 location.reload();
				   }

				}); 
		  }); */
		 $("#company-name").change(function() {
				var slugval1 = $(this).val();
				var	slugval2 = toCamelCase(slugval1);
				var slugval3 = slugify(slugval2);	
				$("#company-slug").val(slugval3);				
		 });		 
		  
		function toCamelCase(str) {
			  // Lower cases the string
			  return str.toLowerCase()
				// Replaces any - or _ characters with a space 
				.replace( /[-_]+/g, ' ')
				// Removes any non alphanumeric characters 
				.replace( /[^\w\s]/g, '')
				// Uppercases the first character in each group immediately following a space 
				// (delimited by spaces) 
				.replace( / (.)/g, function($1) { return $1.toUpperCase(); })
			   
			}

		function slugify(text){
			  return text.toString()
				.replace(/\s+/g, '-')           // Replace spaces with -
				.replace(/[^\u0100-\uFFFF\w\-]/g,'-') // Remove all non-word chars ( fix for UTF-8 chars )
				.replace(/\-\-+/g, '-')         // Replace multiple - with single -
				.replace(/^-+/, '')             // Trim - from start of text
				.replace(/-+$/, '');            // Trim - from end of text
		}

		  $(".removelogo").click(function() {	
				$( "#company-logo" ).click();
		  });
 });
</script>


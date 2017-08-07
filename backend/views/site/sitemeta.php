<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SiteMeta */
/* @var $form ActiveForm */

$this->title = 'Update Logo';
$this->params['breadcrumbs'][] = ['label' => 'Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-create">

    <h1>Update Logos</h1>
	
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

    <?php $form = ActiveForm::begin(['options' => ['enctype'=>'multipart/form-data']]); ?>
	
	<div class="form-group field-sitemeta-rightsidelogo" >
			<label class="control-label" >Right-Side-Logo :&nbsp;</label>
				<img id='rightimagesorce' src="<?=Yii::$app->homeUrl.$right->meta_value ?>" height='100px' width='275px' />
				<a  style="cursor:pointer" class="rightremovelogo" >Change Logo</a>

	<?php echo $form->field($model, 'rightsidelogo')->fileInput(['class'=>'form-control','style'=>'display:none'])->label(False); ?>
	</div>	
	
	<div class="form-group field-sitemeta-leftsidelogo" >
			<label class="control-label" >Left-Side-Logo&nbsp;&nbsp; : &nbsp;</label>
				<img id='leftimagesorce' src="<?=Yii::$app->homeUrl.$left->meta_value ?>" height='100px' width='275px'/>
				<a  style="cursor:pointer" class="leftremovelogo" >Change Logo</a>
		
	<?php echo $form->field($model, 'leftsidelogo')->fileInput(['class'=>'form-control','style'=>'display:none'])->label(False); ?>

   </div>	 
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

	</div>
</div><!-- site-sitemeta -->
</div>

  <script type="text/javascript"> 

    $(function(){	
			$('#sitemeta-rightsidelogo').change( function(event) {
				var tmppath = URL.createObjectURL(event.target.files[0]);
				$("#rightimagesorce").fadeIn("fast").attr('src',tmppath);
			});
  });  
  
   $(function(){	
			$('#sitemeta-leftsidelogo').change( function(event) {
				var tmppath = URL.createObjectURL(event.target.files[0]);
				$("#leftimagesorce").fadeIn("fast").attr('src',tmppath);
			});
  });  
  
 $(document).ready(function () {	 

	$(".rightremovelogo").click(function() {	
				$( "#sitemeta-rightsidelogo" ).click();
		  });
	
	$(".leftremovelogo").click(function() {	
				$( "#sitemeta-leftsidelogo" ).click();
		  });
		  
 }); 
</script>

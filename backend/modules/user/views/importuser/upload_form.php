<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\Company;
use kartik\select2\Select2;


/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Import File';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Import File</h1>
<div class="card">
	<div class="card-body">
	
	<?php 
$sessioncheck = Yii::$app->session->getFlash('Success');
if(isset($sessioncheck) && !empty($sessioncheck)) { ?>
<div id="w3-success-0" class="alert-success alert fade in">
<button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
<?= Yii::$app->session->getFlash('Success'); ?>
</div>
<?php } ?>

<?php 
$sessioncheck = Yii::$app->session->getFlash('Error');
if(isset($sessioncheck) && !empty($sessioncheck)) { ?>
<div id="w3-danger-0" class="alert-danger alert fade in">
<button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
<?= Yii::$app->session->getFlash('Error'); ?>
<?php 
	echo "<br>"; 
	$errordata = Yii::$app->session->getFlash('Error-data'); 
	print implode(",<br> ", $errordata); 	
	?>
</div>
<?php } ?>

	
    <?php $form = ActiveForm::begin( ['options' => ['enctype'=>'multipart/form-data'] ]); ?>
	
	
	
		<?php
		
		if(\Yii::$app->user->can('company manage')) 
		{
			$data = ArrayHelper::map(Company::find()->all(), 'company_id', 'name');			
			echo $form->field($model, 'company_id')->widget(Select2::classname(), [
				'data' => $data,
				'options' => ['placeholder' => 'Select Company Name'],
			]);	
		} else if(\Yii::$app->user->can('company_admin'))
		{		
			echo $form->field($model, 'company_id')->hiddenInput(['value'=>Yii::$app->user->identity->c_id])->label(false);
		}
		
		?>
		
	 <?= $form->field($model, 'upfile')->fileInput(['class'=>'form-control'])->label("Select Import Excel File Only");	?> 
		
    <div class="form-group">
        <?= Html::submitButton('Import',['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

	</div>
</div>




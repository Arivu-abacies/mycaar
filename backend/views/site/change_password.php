<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\SetPassword */
/* @var $form ActiveForm */
?>


	    <h1>Change Password Setting</h1>
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

    <?php $form = ActiveForm::begin(); ?>

	 <?= $form->field($model,'old_password')->passwordInput(['autofocus' => true,'value'=>''])->label('Current Password') ?>	 
	 <?= $form->field($model,'new_password')->passwordInput(['value'=>''])->label('New Password') ?>
	 <?= $form->field($model,'confirm_password')->passwordInput(['value'=>''])->label('Confirm Password') ?>

	 
	 <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div>
</div>

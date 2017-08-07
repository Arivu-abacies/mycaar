<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\SetPassword */
/* @var $form ActiveForm */
?>


	
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
</div>
<?php } ?>

<div class="site-setpassword">

    <?php $form = ActiveForm::begin(['action'=> 'pwdregister']); ?>

	 <?= $form->field($model, 'password_hash')->passwordInput(['autofocus' => true])->label('New Password') ?>
	 
	 <?= $form->field($model, 'id')->hiddenInput(['value'=> $user_id ])->label(false) ?>
	 
	 <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- site-setpassword -->

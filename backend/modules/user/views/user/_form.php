<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\Company;
/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="card">
	<div class="card-body">
	
		<?php $form = ActiveForm::begin(); ?>

		<?php /* $form->field($model, 'username')->textInput(['maxlength' => true]) */ ?>
		
		<?= $form->field($profile, 'firstname')->textInput(['maxlength' => true])->label("First Name *") ?>
		
		<?= $form->field($profile, 'lastname')->textInput(['maxlength' => true])->label("Last Name *") ?>
		
		<?php if($model->isNewRecord){ ?>
		<?= $form->field($model, 'email')->textInput(['maxlength' => true])->label("User Name / Email ID *") ?>
		<?php } else { ?>
		<?= $form->field($model, 'email')->textInput(['maxlength' => true,'readonly'=>'readonly'])->label("Username / Email ID *") ?>
		<?php } ?>
		
		<?= $form->field($model, 'password')->passwordInput()->label("Password (Optional)") ?>
			
		<?= $form->field($model, 'role')->dropDownList(
            $roles,           // Flat array ('id'=>'label')
            ['prompt'=>'--Access Level--']    // options
        )->label("Role *"); ?>

		<?php
		$companies = ArrayHelper::map(Company::find()->all(), 'company_id', 'name');
		echo $form->field($model, 'c_id')->dropDownList(
            $companies,           // Flat array ('id'=>'label')
            ['prompt'=>'--Company--']    // options
        ); ?>
		
		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

		<?php ActiveForm::end(); ?>
	</div>
</div>

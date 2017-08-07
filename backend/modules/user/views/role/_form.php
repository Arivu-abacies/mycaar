<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\Company;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Role */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="card">
	<div class="card-body">

    <?php $form = ActiveForm::begin(); ?>

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

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

	</div>
</div>

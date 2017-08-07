<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\Company;
use common\models\Division;
use common\models\State;
use common\models\Location;
use common\models\Role;
/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */


$selected_company = \Yii::$app->user->identity->c_id;
if(Yii::$app->user->can("company_assessor")){
	$location = Location::find()->where(['company_id'=>$selected_company])->orderBy('name')->all();
	}
else if(Yii::$app->user->can("group_assessor")){
	$access_location = \Yii::$app->user->identity->userProfile->access_location;
	if(!empty($access_location))
	 $useraccesslocation = explode(",",$access_location);
 
	$getlocation = Location::find()->where(['company_id'=>$selected_company])->orderBy('name')->all();
	foreach($getlocation as $key=>$get)
	{
		if(isset($useraccesslocation) && in_array($get->location_id,$useraccesslocation))
		{
		 $location[$key]['location_id']= $get->location_id;
		 $location[$key]['name']= $get->name;
		}
	}	
}
else if(Yii::$app->user->can("local_assessor")){
	$locationid = \Yii::$app->user->identity->userProfile->location;
	$location = Location::find()->where(['company_id'=>$selected_company,'location_id'=>$locationid])->orderBy('name')->all();
}



?>



<div class="card">
	<div class="card-body">

		<?php $form = ActiveForm::begin(); ?>

		<div class="col-md-6 col-sm-6">
		
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
        )->label("User Access Level"); ?>
		
	</div>
	<div class="col-md-6 col-sm-6">
		<?= $form->field($profile, 'employee_number')->textInput(['maxlength' => true]) ?>
	
		<?php
		$division = ArrayHelper::map(Division::find()->where(['company_id' =>Yii::$app->user->identity->c_id])->orderBy('title')->all(), 'division_id', 'title');
			echo $form->field($profile, 'division')->dropDownList(
            $division,           // Flat array ('id'=>'label')
            ['prompt'=>'--Division--']    // options
        );  ?>
		
		
		<?php
		$state = ArrayHelper::map(State::find()->where(['company_id' =>Yii::$app->user->identity->c_id])->orderBy('name')->all(), 'state_id', 'name');
			echo $form->field($profile, 'state')->dropDownList(
            $state,           // Flat array ('id'=>'label')
            ['prompt'=>'--State--']    // options
        );  ?>
		
		
		<?php
		$location = ArrayHelper::map($location, 'location_id', 'name');
			echo $form->field($profile, 'location')->dropDownList(
            $location,           // Flat array ('id'=>'label')
            ['prompt'=>'--Location--']    // options
        );  ?>
		
		<?php
		$role = ArrayHelper::map(Role::find()->where(['company_id' =>Yii::$app->user->identity->c_id])->orderBy('title')->all(), 'role_id', 'title');
			echo $form->field($profile, 'role')->dropDownList(
            $role,           // Flat array ('id'=>'label')
            ['prompt'=>'--Role--']    // options
        );  ?>
	</div>	
	<div class="col-md-12 col-sm-12">
		<div class="form-group" align="center" >
			<?= Html::submitButton($model->isNewRecord ? 'Create ' : 'Update ', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>
	</div>

		
		<?php ActiveForm::end(); ?>
	</div>
</div>

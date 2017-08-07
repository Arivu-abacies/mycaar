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

$this->title = 'Edit My Profile ';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>



<div class="card">
	<div class="card-body">

		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($profile, 'firstname')->textInput(['maxlength' => true])->label("First Name *") ?>
		
		<?= $form->field($profile, 'lastname')->textInput(['maxlength' => true])->label("Last Name *") ?>
		
		<?= $form->field($model, 'email')->textInput(['readonly'=>'readonly'])->label("User Name / Email ID *") ?>

		<?= $form->field($model, 'role')->textInput(['readonly'=>'readonly'])->label("User Access Level") ?>
	    
		<?= $form->field($profile, 'employee_number')->textInput(['maxlength' => true]) ?>
		
		<?php
		$division = ArrayHelper::map(Division::find()->where(['company_id' =>Yii::$app->user->identity->c_id])->all(), 'division_id', 'title');
			echo $form->field($profile, 'division')->dropDownList(
            $division,           // Flat array ('id'=>'label')
            ['prompt'=>'--Division--']    // options
        );  ?>
		
		
		<?php
		$state = ArrayHelper::map(State::find()->where(['company_id' =>Yii::$app->user->identity->c_id])->all(), 'state_id', 'name');
			echo $form->field($profile, 'state')->dropDownList(
            $state,           // Flat array ('id'=>'label')
            ['prompt'=>'--State--']    // options
        );  ?>
		
		
		<?php
		$location = ArrayHelper::map(Location::find()->where(['company_id' =>Yii::$app->user->identity->c_id])->all(), 'location_id', 'name');
			echo $form->field($profile, 'location')->dropDownList(
            $location,           // Flat array ('id'=>'label')
            ['prompt'=>'--Location--']    // options
        );  ?>
		
		<?php
		$role = ArrayHelper::map(Role::find()->where(['company_id' =>Yii::$app->user->identity->c_id])->all(), 'role_id', 'title');
			echo $form->field($profile, 'role')->dropDownList(
            $role,           // Flat array ('id'=>'label')
            ['prompt'=>'--Role--']    // options
        );  ?>
				
		<div class="form-group">
			<?= Html::submitButton('Update My Profile', ['class' => 'btn btn-primary']) ?>
		</div>

		<?php ActiveForm::end(); ?>
	</div>
</div>


</div>

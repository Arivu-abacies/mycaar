<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\SearchUserProfile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-profile-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'profile_id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'firstname') ?>

    <?= $form->field($model, 'lastname') ?>

    <?= $form->field($model, 'position') ?>

    <?php // echo $form->field($model, 'location') ?>

    <?php // echo $form->field($model, 'state') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

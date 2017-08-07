<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\SearchCompany */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'company_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'about_us') ?>

    <?= $form->field($model, 'logo') ?>

    <?= $form->field($model, 'admin') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\SearchProgram */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="program-search">

    <?php $form = ActiveForm::begin([
        'action' => ['reset-users'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'student_id') ?>

    <?= $form->field($model, 'unit_id') ?>



    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

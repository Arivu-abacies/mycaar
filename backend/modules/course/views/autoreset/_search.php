<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\SearchResetSchedule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reset-schedule-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 's_id') ?>

    <?= $form->field($model, 'unit_id') ?>

    <?= $form->field($model, 'cron_time') ?>

    <?= $form->field($model, 'actual_time') ?>

    <?= $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Module */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="module-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'program_id')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'short_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'featured_video_url')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'detailed_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

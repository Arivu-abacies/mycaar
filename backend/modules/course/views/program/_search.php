<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\SearchProgram */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="program-search">

    <?php $form = ActiveForm::begin([
        'action' => ['program-list'],
        'method' => 'get',
    ]); ?>

	<div class='row'>
		<div class='col-lg-10'>
			<?= $form->field($model, 'title',['inputOptions'=>['class'=>'form-control','placeholder'=>'Search Program here']])->label(false) ?>
		</div>
		<div class='col-lg-2'>
			<div class="form-group">
				<?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
			</div>		
		</div>
	</div>

    <?php ActiveForm::end(); ?>

</div>

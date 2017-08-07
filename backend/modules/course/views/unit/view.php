<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use common\models\UnitElement;

$this->registerCssFile(\Yii::$app->homeUrl."css/custom/form-builder.css");
$this->registerCssFile(\Yii::$app->homeUrl."css/custom/form-render.css");
$this->registerJsFile(\Yii::$app->homeUrl."js/custom/form-builder.js");
$this->registerJsFile(\Yii::$app->homeUrl."js/custom/form-render.js");
$this->registerJsFile(\Yii::$app->homeUrl."js/custom/jquery-ui.min.js");
/* @var $this yii\web\View */
/* @var $model common\models\Unit */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Units', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card">
	<div class="card-body">
	
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->unit_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->unit_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
		<?php 
			$element = UnitElement::find()->where(['unit_id'=>$model->unit_id])->one();
			$data = json_decode($element->content);
			$formdata = $data->html;
			$formdata = str_replace(array("\r", "\n"), '', $formdata);
		?>
	<form id="fb-render"></form>
	</div>
</div>
<script>
jQuery(document).ready(function($) {
	var fbRender = document.getElementById('fb-render'),
    formData = '<?=$formdata?>';
	console.log(formData);
	var formRenderOpts = {
		formData: formData
	};
	$(fbRender).formRender(formRenderOpts);
});
</script>
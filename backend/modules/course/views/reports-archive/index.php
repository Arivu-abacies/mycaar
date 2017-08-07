<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

use common\models\ReportsArchive as Archive;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchReportsArchive */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Archived Reports';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reports-archive-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'a_id',
			[
				'attribute' => 'program_id',
				'value' => 'program.title',
				'filter' => Html::activeDropDownList($searchModel, 'program_id', ArrayHelper::map(common\models\Program::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->orderBy('title')->all(), 'program_id', 'title'),['class'=>'form-control','prompt' => 'Select Program']),
			],
            //'program.title',
            //'company_id',
			[
				'attribute' => 'archived_date',
				'value' => 'archived_date',
				'filter' => '<input placeholder="--Search--" type="text" name="SearchReportsArchive[archived_date]" id="archived_date" class="form-control" />',
				'format' => 'html',
			],
/* 			[
				'attribute' => 'archived_date',
				'value' => 'archived_date',
				'filter'=>ArrayHelper::map(Archive::find()->asArray()->groupBy('archived_date')->all(), 'archived_date', 'archived_date'),
				'format' => 'html',
			], */
			[
				'attribute' => 'archive_url',
				 'value'=>function ($data) {
					return Html::a(Html::encode("Download"),\Yii::$app->homeUrl.$data->archive_url, ['target'=>'_blank']);
				},
				'format' => 'raw',
			],
           // 'archived_date',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<script>
$(document).ready(function(){
	$('#archived_date').datepicker({
		autoclose: true, 
		todayHighlight: true,
		format: 'dd-mm-yyyy',
		clearBtn: true
	});
});
</script>
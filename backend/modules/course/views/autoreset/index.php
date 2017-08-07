<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchResetSchedule */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reset Schedules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reset-schedule-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			[
				'label' => 'Program',
				'attribute' => 'program',
				'value' => function($data){
					return $data->unit->module->program->title;
				},
				'filter' => Html::activeDropDownList($searchModel, 'program',
				ArrayHelper::map
					(
						common\models\Program::find()
						->where(['company_id'=>\Yii::$app->user->identity->c_id])
						->orderBy('title')->all(), 'program_id','title'
					),
				['class'=>'form-control','prompt' => 'Select Program']),
				'format' => 'html'
			],
			[
				'label' => 'Module',
				'attribute' => 'module',
				'value' => function($data){
					return $data->unit->module->title;
				},
				'filter' => Html::activeDropDownList($searchModel, 'module',
				ArrayHelper::map
					(
						common\models\Module::find()
						->where(['program_id'=>$searchModel->program])
						->orderBy('title')->all(), 'module_id','title'
					),
				['class'=>'form-control','prompt' => 'Select Module']),
				'format' => 'html'
			],
			[
				'label' => 'Lesson',
				'attribute' => 'unit_id',
				'value' => function($data){
					return Html::a($data->unit->title,['unit/update?id='.$data->unit_id]);
				},
				'filter' => Html::activeDropDownList($searchModel, 'unit_id',
				ArrayHelper::map
					(
						common\models\Unit::find()
						->where(['module_id'=>$searchModel->module])
						->orderBy('title')->all(), 'unit_id','title'
					),
				['class'=>'form-control','prompt' => 'Select Lesson']),
				'format' => 'html'
			],
            //'cron_time',
            //'actual_time',
			[
				'label' => 'Reset Scheduled At',
				'value' => function($data){
					return date('d-M-y', $data->actual_time)." 1.00 AM";
				}
			],
            //'updated_at', By Arivu Client -> Removed
			
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

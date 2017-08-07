<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use common\models\Company;
use common\models\Program;
use common\models\Module;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>
    <h1><?= Html::encode($this->title) ?></h1>
	<div class="card">

		<div class ="card-body">


		<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
		<?=Html::beginForm(['report/reset-units','m_id'=>$m_id],'post');?>
		<p>
			<div class="row">
				<div class="col-md-5" >
					<?=Html::dropDownList(
						'Program',
						$p_id,
						ArrayHelper::map(Program::find()->where(['company_id' =>Yii::$app->user->identity->c_id])->all(),'program_id', 'title'),
						[
						 'id' => 'program_select', 
						 'class' => 'form-control',
						 'prompt'=>'--Select Program--',
						 'onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl('course/report/get-modules?p_id=').'"+$(this).val(), function( data ) {
							$( "select#module_select" ).html( data ).change();
							
						});'
						]
					);?>
				</div>
				<div class="col-md-5" >
					<?=Html::dropDownList(
						'module',
						$m_id,
						ArrayHelper::map(Module::find()->where(['program_id' =>$p_id])->all(),'module_id', 'title'),
						[
						 'id' => 'module_select','prompt' => '--Select Module--','class' => 'form-control','required'=>'required']
					);?>
				</div>
				<div class="col-md-2" >
					<?=Html::submitButton('Reset Selected', ['class' => 'btn btn-info',]);?>
				</div>
			</div>
		</p>
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'columns' => [
				['class' => 'yii\grid\CheckboxColumn'],
				'title:ntext',
				[
					'label'	=> 'Module',
					'value' => 'module.title',
				],				
				[
					'format' => 'raw',
					'label' => 'Reset Users',
					'value' => function ($data) {
							return Html::a('Reset Users',['reset-users','u_id'=>$data->unit_id]);
					},
				],
			],
		]); ?>
		<?= Html::endForm();?> 
		</div>
	</div>
	<script>
		$( "#module_select" ).change(function() {					
			var mod_id = $(this).val();				
			window.location.href = "<?=Yii::$app->homeUrl;?>course/report/reset-units?m_id="+mod_id;
		});		
	</script>	
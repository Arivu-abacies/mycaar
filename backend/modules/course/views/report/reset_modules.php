<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use common\models\Company;
use common\models\Program;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Modules';
$this->params['breadcrumbs'][] = $this->title;
?>
    <h1><?= Html::encode($this->title) ?></h1>
<div class="card">

	<div class ="card-body">


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
		<?=Html::beginForm(['report/reset-modules','p_id'=>$p_id],'post');?>
    <p>
		<div class="row">
			<div class="col-md-8" >
				<?=Html::dropDownList(
					'Program',
					$p_id,
					ArrayHelper::map(Program::find()->where(['company_id' =>Yii::$app->user->identity->c_id])->all(),'program_id', 'title'),
					[
					 'id' => 'program_select', 
					 'class' => 'form-control',
					 'prompt'=>'--Select Program--'
					 ]
				);?>
			</div>
			<div class="col-md-4" >
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
				'label'	=> 'Program',
				'value' => 'program.title',
			],
			//'program.title',
			[
				'label' => 'Reset Units',
				'format' => 'raw',
				'value' => function ($data) {
                        return Html::a('Reset Units',['reset-units','m_id'=>$data->module_id]);
                },
			],
			
        ],
    ]); ?>
	<?= Html::endForm();?> 
	</div>
</div>
<script>
	$( "#program_select" ).change(function() {					
		var programid = $(this).val();				
		window.location.href = "<?=Yii::$app->homeUrl;?>course/report/reset-modules?p_id="+programid;
	});		
</script>	

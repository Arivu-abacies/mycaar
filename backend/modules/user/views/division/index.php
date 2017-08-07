<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Company;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchDivision */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Divisions';
$this->params['breadcrumbs'][] = $this->title;
?>


    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	
<div class="card">
	<div class="card-body">
  
	<div class="row">		
		<div class="col-md-6" >
			<?= Html::a('Create Division', ['create'], ['class' => 'btn btn-success']) ?>
		</div>
		<div class="col-md-6" >
			<a class="btn btn-danger pull-right" id="multi_delete" name="multi_delete" >Multi Delete</a>
		</div>
</div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'], 

			[	
					'class' => 'yii\grid\CheckboxColumn',
					 'checkboxOptions' => function ($data){
						return ['checked' =>false,'value'=>$data['division_id']];
					}, 
				
			],
					
			[
				'attribute' => 'company_id',
				'value' => 'company.name',
				 'filter' => Html::activeDropDownList($searchModel, 'company_id', ArrayHelper::map(Company::find()->all(), 'company_id', 'name'),['class'=>'form-control input-sm','prompt' => 'Company Name']), 
				 'visible' => Yii::$app->user->can('company manage'),
			],
		  
            'title',
            'description',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
	</div>
</div>


 <script type = "text/javascript">
 
     $(document).ready(function(){	
	 
		$("#multi_delete").click(function(){				 
		    var r = confirm("Are you Sure To Delete!");
		    if (r == true) {
				var division_id = $.map($('input[name="selection[]"]:checked'), function(c){return c.value; })
				if($.trim(division_id) === "")
				 {
					alert("Please Select the Checkbox to Delete!!!.");
					return false;
				  }				
				 $.ajax({
				   url: '<?=Yii::$app->homeUrl."user/division/multi-delete"?>',
				   type: 'POST',
				   data: {  division_id: division_id,
				   },
				   success: function(data) {												
						location.reload();
				   }
				 }); 
				}		
			 });			 			 
	});
		 
      </script> 

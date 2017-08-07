<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\MyCaar;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchCompany */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Companies';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="card">

    <div class="card-body">

    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

  <div class="row">
	<div class="col-md-6">
        <?= Html::a('Create Company', ['create'], ['class' => 'btn btn-success']) ?>
	</div>	
	<div class="col-md-6">	
		<a class="btn btn-danger pull-right" style="margin-right: 10px;" id="multi_delete" name="multi_delete" >Hide </a>
	</div>
</div>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',           
			[
				'attribute' => 'admin',
				'value' => 'companyAdmin.email',
				'filter' => Html::activeDropDownList($searchModel, 'admin', ArrayHelper::map(MyCaar::getUserAllByrole("company_admin"), 'id', 'email'),['class'=>'form-control input-sm','prompt' => 'Company Admin']),
			],
			
			[		

					'class' => 'yii\grid\CheckboxColumn',
					'header' => Html::checkBox('selection_all', false, [
						'class' => 'select-on-check-all',
						'label' => 'Hide',
						"style" => "display:none",
					]), 		
					'checkboxOptions' => function ($data){
						return ['checked' =>(!empty($data['status']))?true:false,'value'=>$data['company_id'],];
					}, 	
					
			],
			
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
	</div>
</div>

 <script type = "text/javascript">
 
     $(document).ready(function(){	
	 
		$("#multi_delete").click(function(){				 
		    var r = confirm("Are you Sure To Hide this Company!");
		    if (r == true) {
				
				var hidecompany_id = $.map($('input[name="selection[]"]:checked'), function(c){ return c.value; });				
				var showcompany_id = $.map($('input[name="selection[]"]:not(:checked)'), function(c){return c.value; });
				
				 $.ajax({
				   url: '<?=Yii::$app->homeUrl."user/company/multi-hide-company"?>',
				   type: 'POST',
				   data: {  
					hidecompany_id: hidecompany_id,
					showcompany_id: showcompany_id,
				   },
				   success: function(data) {	
						location.reload();
				   }
				 }); 
				}		
			 });
	 });

</script>	

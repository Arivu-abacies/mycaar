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
						return ['checked' =>(!empty($data['status']))?true:false,'value'=>$data['company_id'],'class' => 'checkbox-row','data-value'=>$data['message']];
					},
							
					
			],
			
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
	</div>
</div>



<!-- New User style pop up -->
	<!-- Modal -->
<!-- BEGIN FORM MODAL MARKUP -->
 <a id="company_message" name="company_message" style="cursor:pointer; display:none;" data-toggle="modal" data-target="#companydescription" > Message Content </a>
 
<div class="modal fade" id="companydescription" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="z-index: 9999 !important;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="formModalLabel">Message For Hide This Company To Display</h4>
			</div>
			<form class="form-horizontal" role="form" >
				<div class="modal-body">
				    <div class="form-group">
						<div class="col-sm-12">
							<label for="email1" class="control-label">Message</label>
						</div>
						<div class="col-sm-12">
							<input type="text" name="message" id="message" class="form-control" required >
							<input type="hidden" name="company_id" id="company_id" class="form-control" >
						</div>
						<div style="color:red;margin-left:20px" class="error-msg error-message"> </div>
					</div>											
				</div>
				<div class="modal-footer">
					<!--<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>-->
					<button type="button" id="updatemsg" name="updatemsg" class="btn btn-primary">Update Message</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- END FORM MODAL MARKUP -->
<!-- New User style pop up -->


 <script type = "text/javascript">
 
     $(document).ready(function(){	
	 
		/* $("#multi_delete").click(function(){				 
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
			 }); */
			 
		$("#updatemsg").click(function(){	 
			var message = $("#message").val();
			var company_id = $("#company_id").val();
			
			if($.trim(message) == "") { 
				$(".error-message").html("Please Fill Message Field"); 
				return false;
			} 
			if($.trim(company_id) == "") { 
				alert("Oops!!! Something went Wrong. Please Refresh this page.")
				return false;
			} 
			
			$.ajax({
				url: '<?=Yii::$app->homeUrl."user/company/message-company"?>',
				type: 'POST',
				data: {  
					   company_id: company_id,
					   message: message,
					},
				success: function(data) {	
						$( ".close" ).trigger( "click" );
						alert(" Message Updated For This Company !!!. ");
					}
			    }); 
					
		});
			
			 $(".checkbox-row").click(function(){
					if($(this).is(':checked'))
					{
						var hidecompany_id = $(this).val();
						var r = confirm("Are You Sure Want To Hide This Company!");
						if (r == true) {
						var messagecompany_id = $(this).attr('data-value');
						
						   $(".error-message").html("");
						   $("#company_id").val(hidecompany_id);
						   $("#message").val(messagecompany_id);
						   $( "#company_message" ).trigger( "click" );	
						   
						$.ajax({
							   url: '<?=Yii::$app->homeUrl."user/company/hide-company"?>',
							   type: 'POST',
							   data: {  
								hidecompany_id: hidecompany_id,
							   },
							   success: function(data) {	
									alert(" This Company Details Has Temporarily Hidden Successfully!!!.");
							   }
							 }); 
						} else {
							return false
						}
						
					} else {
						var showcompany_id = $(this).val();
						var r = confirm("Are You Sure Want To Show This Company!");
						if (r == true) { 
							$.ajax({
							   url: '<?=Yii::$app->homeUrl."user/company/show-company"?>',
							   type: 'POST',
							   data: {  
								showcompany_id: showcompany_id,
							   },
							   success: function(data) {	
									alert(" Now The Company Details Are Visible Successfully!!!.");
							   }
							 });
						} else {
							return false
						}
						
					}
			 });
	 });

</script>	

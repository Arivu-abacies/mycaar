<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use common\models\Company;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Programs';
$this->params['breadcrumbs'][] = $this->title;
$homeUrl = Yii::$app->homeUrl;
?>
<link type="text/css" rel="stylesheet" href="<?=Yii::$app->homeUrl?>css/theme-default/libs/nestable/nestable.css?1423393667" />
<h1><?= Html::encode($this->title) ?></h1>
<div class="card">

	<div class ="card-body">


    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Add Program', ['create'], ['class' => 'btn btn-default']) ?>
    </p>
	<div class='row'>
		<div class='col-lg-12'>
			
					<?php foreach($dataProvider->models as $program){
						echo 					
						"<div class='dd nestable-list' id='list_{$program->program_id}'>
				<ol class='dd-list'><li class='dd-item tile' data-id='$program->program_id'>
							<div class='btn btn-primary' style='min-height:35px;'><span class='pull-left'><i class='fa fa-list'></i> $program->title</span>
							<span class='pull-right text-default'>
								<a href='{$homeUrl}course/program/view?id={$program->program_id}' title='View Program' style='padding-right:3px'><span class='glyphicon glyphicon-eye-open'></span></a>
								<a href='{$homeUrl}course/program/update?id={$program->program_id}' title='Update Program' style='padding-right:3px'><span class='glyphicon glyphicon-pencil'></span></a>
								<a href='{$homeUrl}course/module/create?p_id={$program->program_id}' title='Add Course' style='padding-right:3px'><span class='glyphicon glyphicon-plus'></span></a>
								<a href='{$homeUrl}course/program/delete?id={$program->program_id}' title='Delete Program' data-confirm='Are you sure you want to delete this item?' data-method='post' style='padding-right:3px'><span class='glyphicon glyphicon-trash'></span></a>
							</span>
							</div>
						";
						$modules = $program->modules;
						//if(count($modules)>0){
							echo "<ol class='dd-list'>";
							foreach($modules as $module){
								echo "
									<li class='dd-item' data-id='$module->module_id'>
										<div class='btn btn-default' style='min-height:35px;'><span class='pull-left'>$module->title</span>
											<span class='pull-right text-primary'>
												<a href='{$homeUrl}course/module/update?id={$module->module_id}' title='Update Course' style='padding-right:3px'><span class='glyphicon glyphicon-eye-open'></span></a>
												<a href='{$homeUrl}course/unit/create?m_id={$module->module_id}' title='Add Lesson' style='padding-right:3px'><span class='glyphicon glyphicon-plus'></span></a>
												<a href='{$homeUrl}course/module/delete?id={$module->module_id}' title='Delete Course' data-confirm='Are you sure you want to delete this item?' data-method='post'  style='padding-right:3px'><span class='glyphicon glyphicon-trash'></span></a>";
										if($module->status)
											echo "<span class='glyphicon glyphicon-ok' title='published'></span>";
										else
											echo "<span class='glyphicon glyphicon-remove' title='unpublished'></span>";
										echo "</span>										
										</div>
									";
								$units = $module->units;
								if(count($units)>0){
									echo "<div data-type='unit' class='dd nestable-unit-list' id='unit_{$module->module_id}'><ol class='dd-list'>";
									foreach($units as $unit){
										echo "
									<li data-type='unit' data-m_id='$module->module_id' class='dd-item list-group' data-id='$unit->unit_id'><div class='dd-handle btn btn-default-light'></div>
										<div class='btn btn-default-bright' style='min-height:35px;'><span class='pull-left'>$unit->title</span>
											<span class='pull-right text-primary-dark'>
												<a href='{$homeUrl}course/unit/update?id={$unit->unit_id}' title='View Lesson' style='padding-right:3px'><span class='glyphicon glyphicon-eye-open'></span></a>
												<a href='{$homeUrl}course/unit/delete?id={$unit->unit_id}' title='Delete Lesson' data-confirm='Are you sure you want to delete this item?' data-method='post' style='padding-right:3px'><span class='glyphicon glyphicon-trash'></span></a>";
												
										if($unit->status)
											echo "<span class='glyphicon glyphicon-ok' title='published'></span>";
										else
											echo "<span class='glyphicon glyphicon-remove' title='unpublished'></span>";
										
										echo "</span>											
										</div>
									</li>";
									}
									echo "</ol></div>";
								}
								echo "</li>";
							}	
							echo "</ol>";
						//}
						echo "</li></ol>
			</div>";
					}?>
				<!--end .dd.nestable-list -->
		</div><!--end .col -->

	</div><!--end .row -->
	</div>
</div>
<script src="<?=Yii::$app->homeUrl?>js/libs/nestable/jquery.nestable.js"></script>
<script>
//$(document).ready(function(){
/* 	$('.nestable-list').nestable({
		//maxDepth:2,
		group:$(this).attr('id'),
	}).on('change', updateOutput); */
 $('.nestable-unit-list').each(function(i, obj) {
    //test
	var elem_id = $(this).attr('id');
	console.log(elem_id);
	$('#'+elem_id).nestable({
		//group : 0,
		maxDepth: 3
	}).on('beforeDragEnd', function(event, item, source, destination, position, feedback) {
			console.log('dest',destination[0]);
			console.log('src',source[0]);
			if (source[0] != destination[0]) { feedback.abort = true; return; }
		})
	.on('change',function(){
		updateOutput($('#'+elem_id).nestable('serialize'));
		//$('.nestable-list').nestable();
	 });
	}); 
//});
/* 	$('.nestable-list').nestable({
		group : 0,
		maxDepth: 3,
	}).on('beforeDragEnd', function(event, item, source, destination, position, feedback) {
			console.log('dest',destination[0]);
			console.log('src',source[0]);
			var data_type = item.attr('data-type');
			if(data_type == 'unit'){
				return;
			} 
			if (source[0] != destination[0]) { feedback.abort = true; return; }
		})
		.on('change',function(){
			updateOutput($('.nestable-list').nestable('serialize'));
		});  */
	/* 	$('.nestable-unit-list').nestable({
		group : 0,
		maxDepth: 3,
		}).on('beforeDragEnd', function(event, item, source, destination, position, feedback) {
			console.log('dest',destination[0]);
			console.log('src',source[0]);
			if (source[0] != destination[0]) { feedback.abort = true; return; }
		})
		.on('change',function(){
			updateOutput($('.nestable-unit-list').nestable('serialize'));
			//$('.nestable-list').nestable();
		 }); */
		
function updateOutput(output){
	console.log('output',output);
 	$.ajax({
		url:'<?=Url::to(['unit/re-order'])?>',
		type: 'POST',
		data: {data:output},
		dataType: 'json',
		success:function(response){
			console.log(response);
		},
		error:function(response){
			console.log(response);
		}
	})  
	console.log('changed');
}
</script>

//controller
	public function actionReOrder(){
		$data = \Yii::$app->request->post()['data'];
		//print_r($data);die;
		foreach($data as $order=>$module){
			$module = $this->findModel($module['id']);
			$module->module_order = $order;
			$module->save();
		}
		return true;
	}
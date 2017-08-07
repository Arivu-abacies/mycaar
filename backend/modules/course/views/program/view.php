<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
$homeUrl = Yii::$app->homeUrl;
$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Programs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<link type="text/css" rel="stylesheet" href="<?=Yii::$app->homeUrl?>css/theme-default/libs/nestable/nestable.css?1423393667" />
<h1><?= Html::encode($this->title) ?></h1>
<div class="card">

	<div class ="card-body">

    

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->program_id], ['class' => 'btn btn-primary']) ?>		
        <?= Html::a('Delete', ['delete', 'id' => $model->program_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
		<?= Html::a('All Programs', ['program-list'], ['class' => 'btn btn-info']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'program_id',
            'title:ntext',
            'company.name',
            'description:ntext',
        ],
    ]) ?>
	<h3><?= 'Modules ' ?><?= Html::a('Add new', ['module/create','p_id'=>$model->program_id], ['class' => 'btn btn-default']) ?></h3>
		<div class='col-lg-12'>
			
					<?php 
					echo "<div class='dd nestable-list' id='list'>";
					echo "<ol class='dd-list'>";
					foreach($dataProvider->models as $module){
						//$modules = $program->modules;
						if(count($dataProvider->models)>0){							
							//foreach($modules as $module){
								echo "
									<li class='dd-item list-group' data-id='$module->module_id'>
										<div class='dd-handle btn btn-default-light'></div><div class='btn btn-default' style='min-height:35px;'><span class='pull-left'>$module->title</span>
											<span class='pull-right text-primary'>
												<a href='{$homeUrl}course/module/update?id={$module->module_id}' title='Update Module' style='padding-right:3px'><span class='glyphicon glyphicon-eye-open'></span></a>
												<a href='{$homeUrl}course/unit/create?m_id={$module->module_id}' title='Add Lesson' style='padding-right:3px'><span class='glyphicon glyphicon-plus'></span></a>
												<a href='{$homeUrl}course/module/delete?id={$module->module_id}' title='Delete Module' data-confirm='Are you sure you want to delete this item?' data-method='post'  style='padding-right:3px'><span class='glyphicon glyphicon-trash'></span></a>";
										if($module->status)
											echo "<span class='glyphicon glyphicon-ok' title='published'></span>";
										else
											echo "<span class='glyphicon glyphicon-remove' title='unpublished'></span>";
										echo "</span>										
										</div>
									";
								echo "</li>";
							//}	
							
						}

					}						echo "</ol>";
						echo "
			</div>";?>
				<!--end .dd.nestable-list -->
		</div><!--end .col -->
	</div>
</div>
<script src="<?=Yii::$app->homeUrl?>js/libs/nestable/jquery.nestable.js"></script>
<script>
	$('.nestable-list').nestable({
	group : 0,
	maxDepth: 3,
	}).on('beforeDragEnd', function(event, item, source, destination, position, feedback) {
		console.log('dest',destination[0]);
		console.log('src',source[0]);
		if (source[0] != destination[0]) { feedback.abort = true; return; }

	})
	.on('change',function(){
		updateOutput($('.nestable-list').nestable('serialize'));
		//$('.nestable-list').nestable();
	 });
	 
	function updateOutput(output){
		console.log('output',output);

		$.ajax({
			url:'<?=Url::to(['module/re-order'])?>',
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

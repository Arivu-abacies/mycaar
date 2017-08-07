<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use common\models\UnitElement;
use yii\bootstrap\ActiveForm;
$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Units', 'url' => ['Test']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card" style="padding-top:20px">
<div class="card-head regular style-info">
	<header>
		<div class="lesson">
			<div class="text-lg"><strong><label>Lesson:</label> <?=$this->title?></strong></div>
			<div class="text-sm"><label>Module:</label> <?=$model->module->title?></div>
			<div class="text-sm"><label>Program:</label> <?=$model->module->program->title?></div>
			<div class="text-sm"><label>User:</label> <?=$user->userProfile->fullname?></div>	
		</div>
	</header>
</div>
<div class="card-body">
	<form class="form" method="post">
		<?php 
		if($errors){
			echo '<div class="alert alert-danger" role="alert">
					<strong>Oh wait!</strong> All questions are mandatory.
				</div>';
		}
		foreach($questions as $question){
			echo '<div class="small-padding">';
					echo "<h3>{$question->question}</h3>";
					echo "<p>{$question->description}</p>";
					echo '<div class="form-group">';
					//foreach($question->options as $option){
						echo 
						"<div class='radio radio-styled radio-info'>
							<label>
							<input type='radio' name='$question->cq_id' value='yes'>
							<span>Yes</span>
							</label>
						</div>";
						echo 
						"<div class='radio radio-styled radio-info'>
							<label>
							<input type='radio' name='$question->cq_id' value='no'>
							<span>No</span>
							</label>
						</div>";
					//}
					//echo "<div class='text-danger' id='help_{$question->aq_id}'></div>";
					echo "</div>";
				
			echo '</div>';
		}
		?>
		<?php if(!$final) { ?> 
		<div class="form-group">
			<input name= "save" type="submit" class="btn btn-lg ink-reaction btn-info" value="Next"/>
		</div>
		<?php } else { ?>
		<div class="form-group">
			<input name= "save_n_exit" type="submit" class="btn btn-lg ink-reaction btn-info" value="Finish"/>
		</div>		
		<?php } ?>
	</form>
</div>
</div>
<script>
	var answers = '<?=$answers ?>';
	if(answers){
		var answers = $.parseJSON(answers);
		//console.log(answers);
		$(answers).each(function(i,val){
			$.each(val,function(k,v){
				//console.log(k+" : "+ v);  
				if(v !== ''){
					var qstn = $("input[name="+k+"]") ;
					if(!qstn.length || qstn.length == 0)
						var qstn = $("input[data_q="+k+"]");
					//console.log(qstn);
					var type = qstn.attr("type");
					switch(type){
						case "checkbox":
							$.each(v, function(p,q){
								//console.log(p+" : "+ q);  
								var qstn = $("input[data_q="+k+"][value="+q+"]");
								qstn.attr("checked",true);
							});
							break;
						case "radio":
							var qstn = $("input[name="+k+"][value="+v+"]");
							qstn.attr("checked",true);
							break;
						case "file":
						case "text":
							qstn.val(v);
							break;
						
					}
				}else{
					$("#help_"+k).html("Please answer this question dude");
				}
			});
		});		
	}

</script>
<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use common\models\UnitElement;

$this->registerCssFile(\Yii::$app->homeUrl."css/custom/form-builder.css");
$this->registerCssFile(\Yii::$app->homeUrl."css/custom/form-render.css");
$this->registerJsFile(\Yii::$app->homeUrl."js/custom/form-builder.js");
$this->registerJsFile(\Yii::$app->homeUrl."js/custom/form-render.js");
$this->registerJsFile(\Yii::$app->homeUrl."js/custom/jquery-ui.min.js");
/* @var $this yii\web\View */
/* @var $model common\models\Unit */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Units', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-md-12 small-padding">
	<div class="card card-underline">
		<div class="card-head">
			<ul class="nav nav-tabs pull-right" data-toggle="tabs">
				<li class="active"><a href="#learning">Learning Page</a></li>
				<li><a href="#aw_test">Awareness Test Page</a></li>
				<!--<li><a href="#cap_test">Capability Test Page</a></li>-->
			</ul>
			<header>PREVIEW &nbsp; <a class="btn btn-sm btn-info pull-right" href="<?=Url::to(['unit/update','id'=>$model->unit_id])?>">Back</a></header>
		</div>
		<div class="card-body tab-content">
			<div class="tab-pane active" id="learning">
			<p>		
				<?php 
				$element = UnitElement::find()->where(['unit_id'=>$model->unit_id])->one();
				$data = json_decode($element->content);
				$formdata = $data->html;
				$formdata = str_replace("'", "\'", $formdata);
				$formdata = str_replace(array("\r", "\n"), '', $formdata);
				?>
				<form id="fb-render"></form>
			</p>
			</div>
			<div class="tab-pane" id="aw_test">
				<form class="form" method="post">
					<?php
					foreach($questions as $question){
						echo '<div class="small-padding">';
						switch($question->question_type){
							//////RADIO GROUP//////
							case "radio-group":
								$q = html_entity_decode($question->question);
								$description = html_entity_decode($question->description);
								echo "<h3>{$q}</h3>";
								echo "<p>{$description}</p>";
								echo '<div class="form-group">';
								foreach($question->options as $option){
									echo
									"<div class='radio radio-styled radio-info'>
										<label>
										<input type='radio' name='$question->aq_id' value='{$option->option_id}'>
										<span>{$option->answer}</span>
										</label>
									</div>";
								}
								//echo "<div class='text-danger' id='help_{$question->aq_id}'></div>";
								echo "</div>";
								break;
							/////////CHECKBOX GROUP//////
							case "checkbox-group":
								$q = html_entity_decode($question->question);
								$description = html_entity_decode($question->description);
								echo "<h3>{$q}</h3>";
								echo "<p>{$description}</p>";
								echo '<div class="form-group">';
								foreach($question->options as $option){
									echo
									"<div class='checkbox checkbox-styled checkbox-info'>
										<label>
										<input type='checkbox' data_q='{$question->aq_id}' name='{$question->aq_id}[]' value='{$option->option_id}'>
										<span>{$option->answer}</span>
										</label>
									</div>";
								}
								//echo "<div class='text-danger' id='help_{$question->aq_id}'></div>";
								echo '</div>';
								break;
							case "fileupload":
								$q = html_entity_decode($question->question);
								$description = html_entity_decode($question->description);
								echo "<h3>{$q}</h3>";
								echo "<p>{$description}</p>";
								echo '<div class="form-group">';
								echo
								"<div class='border-gray small-padding form-group'>
									<input name='$question->aq_id' type='file' class='form-control'>
								</div>";
								//echo "<div class='text-danger' id='help_{$question->aq_id}'></div>";
								echo '</div>';
								break;
							case "text":
								$q = html_entity_decode($question->question);
								$description = html_entity_decode($question->description);
								echo "<h3>{$q}</h3>";
								echo "<p>{$description}</p>";
								echo '<div class="form-group">';
								echo
								"<div class='form-group'>
									<input name='$question->aq_id' type='text' class='form-control' placeholder='' value=''>
									<label>Enter Answer here</label>
								</div>";
								//echo "<div class='text-danger' id='help_{$question->aq_id}'></div>";
								echo '</div>';
								break;
							case "img":
								$q = html_entity_decode($question->question);
								$description = html_entity_decode($question->description);
								echo "<h3>{$q}</h3>";
								echo "<p>{$description}</p>";
								//echo '<div class="form-group">';
								echo
								"<div class='form-group'>
									<img src='$question->src' height='300px'>
								</div>";
								//echo "<div class='text-danger' id='help_{$question->aq_id}'></div>";
								//echo '</div>';
								break;	
							case "filedownload":
								$q = html_entity_decode($question->question);
								$description = html_entity_decode($question->description);
								echo "<h3>{$q}</h3>";
								echo "<p>{$description}</p>";
								//echo '<div class="form-group">';
								echo
								"<div class='form-group'>
									<a href='$question->src'>
										<button type='button' class='btn btn-info'>Download File</button>
									</a>
								</div>";
								//echo "<div class='text-danger' id='help_{$question->aq_id}'></div>";
								//echo '</div>';
								break;						
						}

						echo '</div>';
					}
					?>
					<button onclick="return false;" class="btn btn-lg ink-reaction btn-info">Submit Answer/s</button>
					<button onclick="return false;" class="btn btn-lg ink-reaction btn-info">Save & Return to Dashboard</button>	
				</form>
			</div>
			<!--<div class="tab-pane" id="cap_test">		
			<?php
			/* foreach($cp_questions as $question){
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
			} */
			?>
			<button onclick="return false;" class="btn btn-lg ink-reaction btn-info">Finish Answer/s</button>
			</div>-->
		</div>
	</div><!--end .card -->
	<em class="text-caption">Preview</em>
</div>
<script>
jQuery(document).ready(function($) {
	var fbRender = document.getElementById('fb-render'),
    formData = '<?=$formdata?>';
	console.log(formData);
	var formRenderOpts = {
		formData: formData
	};
	$(fbRender).formRender(formRenderOpts);
});
var hash = window.location.hash;
if(hash != '')
	$("a[href='" + hash + "']").tab("show");
</script>

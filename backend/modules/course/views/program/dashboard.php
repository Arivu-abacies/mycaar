<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\helpers\Url;

use common\models\Company;
use common\models\User;
use common\models\Program;




/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reports';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(\Yii::$app->homeUrl."css/custom/w3.css");
//if($params){
if(Yii::$app->user->can("superadmin"))
	$selected_company = isset($params['company'])?$params['company']:\Yii::$app->user->identity->c_id;
else
	$selected_company = \Yii::$app->user->identity->c_id;


?>
<script src="<?=Yii::$app->homeUrl;?>js/js/pie-chart.js" type="text/javascript"></script>
<link href="<?=Yii::$app->homeUrl;?>js/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
	
<h3>Progress for <?=  \Yii::$app->user->identity->fullname ?></h3>
<?php 

	
	if(isset($programs) && !empty($programs)){
		/* echo "<pre>";
			print_r($programs);
			exit; */
		foreach($programs as  $key=>$tmp)
		{	
			/* echo $tmp->program_id;
			echo "<br>"; */
			$overallprec = 0;
		
		 	$program_prec = $tmp->getAllEnrolledUserProgram($selected_company,$tmp->program_id);
			
			
			$overallprec = $program_prec;		
			//	echo $tmp->program_id." <--> ".$tmp->title." <--> ".$overallprec."<br>";
			
			echo '<div class="col-md-3" ><div class="for-height" style="height:70px !important;"><label>'.$tmp->title.'</label></div><div data-id="'.$tmp->program_id.'" id="demo-pie-'.$tmp->program_id.'" class="pie-title-center dataclick" data-percent="'.$overallprec.'"> <span class="pie-value"></span> </div></div>'; 
			 
		}
	}	


?>

<form target="_blank" style="display:none" name="formsubmit" id="formsubmit" method="post" action="<?= Url::to(['report/search'])?>">
	<input type="hidden" name="program" class="form-control"  id="program" value="0" />
		<input type="hidden" class="form-control" name="page" id="page" value="0" />
	<div class="form-group">
		<button type="submit" id="submit_check" class="btn btn-primary">Search</button>  
	</div>
</form>	

<script type="text/javascript">

        $(document).ready(function () {
		<?php if(isset($programs) && !empty($programs)){
		foreach($programs as  $tmp)
			{
		?>	
            $('#demo-pie-<?= $tmp->program_id ?>').pieChart({
                barColor: '#68b828',
                trackColor: '#eee',
                lineCap: 'square',
                lineWidth: 14,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });
		<?php } } ?>	
		
			$(".dataclick").click(function(){
				var id = $(this).attr('data-id');
				$("#program").val(id);
				$("#formsubmit").submit();
			});
          });
	</script>


<style>
.pie-title-center {
  display: inline-block;
  position: relative;
  text-align: center;
}

.pie-value {
  display: block;
  position: absolute;
  font-size: 14px;
  height: 40px;
  top: 50%;
  left: 0;
  right: 0;
  margin-top: -20px;
  line-height: 40px;
}
.dataclick{
	cursor:pointer;
}
</style>	
	
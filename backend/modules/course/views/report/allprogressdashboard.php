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
	$company_id = isset($params['company'])?$params['company']:\Yii::$app->user->identity->c_id;
else
	$company_id = \Yii::$app->user->identity->c_id;


	$selected_user = isset($params['user'])?$params['user']:'';
	$selected_program = isset($params['program'])?$params['program']:'';
	$firstname = isset($params['firstname'])?$params['firstname']:'';
	$lastname = isset($params['lastname'])?$params['lastname']:'';
	$role = isset($params['role'])?$params['role']:'';
	$division = isset($params['division'])?$params['division']:'';
	$location = isset($params['location'])?$params['location']:'';
	$state = isset($params['state'])?$params['state']:'';
	$selected_page = isset($params['page'])?$params['page']:0;
	

?>
<script src="<?=Yii::$app->homeUrl;?>js/js/pie-chart.js" type="text/javascript"></script>
<link href="<?=Yii::$app->homeUrl;?>js/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<h4></h4>
<?php 

	
	if(isset($programs) && !empty($programs)){		
		foreach($programs as  $key=>$tmp)
		{				
			$overallprec = 0;
			$program_id = $tmp->program_id;
		 	$program_prec = $tmp->getAllEnrolledUserProgram($company_id,$program_id,$firstname,$lastname,$role,$division,$location,$state);	
			if($program_prec !== false)
			{				
			$overallprec = $program_prec;					
			echo '<div class="col-md-3 pie-chart-align" ><div class="for-height" style="height:70px !important;"><label>'.$tmp->title.'</label></div><div data-id="'.$tmp->program_id.'" id="demo-pie-'.$tmp->program_id.'" class="pie-title-center dataclick" data-percent="'.$overallprec.'"> <span class="pie-value"></span> </div></div>'; 
			}	
		}
	}	


?>

<form  style="display:none" name="formsubmit" id="formsubmit" method="post" action="<?= Url::to(['report/search'])?>">
	<input type="hidden" name="company" class="form-control"  id="company" value="<?=$company_id ?>" />
	<input type="hidden" name="program" class="form-control"  id="program" value="0" />
	<input type="hidden" name="firstname" class="form-control"  id="firstname" value="<?=$firstname ?>" />
	<input type="hidden" name="lastname" class="form-control"  id="lastname" value="<?=$lastname ?>" />
	<input type="hidden" name="role" class="form-control"  id="role" value="<?=$role ?>" />
	<input type="hidden" name="division" class="form-control"  id="division" value="<?=$division ?>" />
	<input type="hidden" name="location" class="form-control"  id="location" value="<?=$location ?>" />
	<input type="hidden" name="state" class="form-control"  id="state" value="<?=$state ?>" />
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
  color: #68B828;
  font-weight: bold;
  font-size: 20px;
  display: block;
  position: absolute;
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
.pie-chart-align {
	text-align: center;
}
.for-height {
    width: 250px;
}
.for-height label {
    font-weight: 600;
}
</style>	
	
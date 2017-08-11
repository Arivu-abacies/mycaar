<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\helpers\Url;

use common\models\Program;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reports';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(\Yii::$app->homeUrl."css/custom/w3.css");
?>

 <script src="<?=Yii::$app->homeUrl;?>js/js/pie-chart.js" type="text/javascript"></script>
	<link href="<?=Yii::$app->homeUrl;?>js/css/jquerysctipttop.css" rel="stylesheet" type="text/css">

    
	<div class="mdl-section-check">
    <div class="mdl-grid mdl-home ">
					<div class="mdl-cell mdl-cell-8-col" style="margin: 0px 32px 0px 4px !important;">
						<h1 class="mdl-sidebar"><strong>Home Page</strong></h1>
					</div>
					
	</div>
		<div class="mdl-grid">
				<div class="mdl-cell mdl-cell-8-col">
					<span class="mdl-welcome"><h3>Progress for <?=\Yii::$app->user->identity->fullname?></h3></span>
				</div>
		</div>
	<?php if(Yii::$app->session->getFlash('error')!='') {?>
	<div class="alert alert-danger" role="alert">
		<strong> <?= Yii::$app->session->getFlash('error'); ?>.</strong>
	</div>
	
	<?php 
	}
	foreach($programs as $program)
	{
		$modules = $program->publishedModules;
		// mdl-cell This class is removed from [ mdl-cell-8-col ] for Alignment into stright line - 56
		if(count($modules) > 0 && count($program->programEnrollments) > 0)
		{	  
			echo '<div class="horizontal al_cpp_category_16">'; 
			$overalluser = 0;
			$countprogress = 0;		
			foreach($users as $user){
				if($user->user->isEnrolled($program->program_id)){
					//$progress = 0;
					$overalluser = $overalluser + 1;
					$newprogress = $progress = $user->user->getProgramProgress($program->program_id);
					$countprogress = $countprogress + $newprogress;
	
				}
			}
	

		$overallprec = $countprogress/$overalluser;
		
		echo '<div class="col-md-3 pie-chart-align" ><div class="for-height" style="height:70px !important;"><label>'.$program->title.'</label></div><a href="'.Url::to(["site/user-program", "id"=>$program->program_id] ).'" ><div data-id="'.$program->program_id.'" id="demo-pie-'.$program->program_id.'" class="pie-title-center dataclick" data-percent="'.$overallprec.'"> <span class="pie-value"></span> </div></a></div>';
		
		echo "</div>";
		?>


		<?php
		} 
	}
	
	//FOR DEBUG
	//foreach($users as $user){
	//$progress = $user->user->getProgramProgress(1);
	//} 
	?>

		<!-- Modal -->
   </div>	
	<script>		
		 $( window ).on( "load", function() {
				 var height = $(document).scrollTop();				
				 var top1 = parseInt(height) - parseInt(300);				 
				 $("html,body").animate({scrollTop:top1}, 1000);				
			});
			
			
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

	</script>
	<style>
	.modal-backdrop{
		z-index:0 !important
	}
	body{	background:white;	}
	</style>

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
   text-align:center;
   }
   .for-height {
    width: 250px;
}
</style>	
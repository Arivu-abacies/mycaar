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

<!--<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->
    <!--<link rel="stylesheet" href="http://zavoloklom.github.io/material-design-iconic-font/css/docs.md-iconic-font.min.css">-->
    
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
		
		echo '<div class="col-md-3" ><div class="for-height" style="height:70px !important;"><label>'.$program->title.'</label></div><a href="'.Url::to(["site/user-program", "id"=>$program->program_id] ).'" target="_blank"><div data-id="'.$program->program_id.'" id="demo-pie-'.$program->program_id.'" class="pie-title-center dataclick" data-percent="'.$overallprec.'"> <span class="pie-value"></span> </div></a></div>';
		
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
			<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog">
				
				  <!-- Modal content-->
				  <div class="modal-content" style="margin:30% 0% 0% 3%" >
					<!--<div class="modal-header style-primary">
					  <h4 class="modal-title text-bold text-xxl">Sorry!</h4>
					</div>-->
					<div class="modal-body text-medium">
					<!--  <p>Sorry, you may only access this capability test if you are an approved assessor. Please contact your assessor or manager to complete this step!</p>-->
					  
    				  <div class="check_Popup_Capability">					
						<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
						<i class="fa fa-times " data-dismiss="modal" aria-hidden="true"></i>
						<p class="capability_3">Sorry</p>
						<p class="capability_1">You are not able to complete your own capabilty test.</p>
						<p class="capability_4">Please refer to your assigned coach or assessor to complete this step.</p>
						<button class="capability_2 " data-dismiss="modal" aria-hidden="true" >Go Back</button>
    				 </div> 
    				 
					</div>
				  </div>
				  
				</div>
			</div>
			
			
			<div class="modal fade" id="myModal4" role="dialog">
				<div class="modal-dialog">
				
				  <!-- Modal content-->
				  <div class="modal-content" style="margin:30% 0% 0% 3%" >
				
					<div class="modal-body text-medium">
					  <!--<img src="<?=Yii::$app->urlManagerBackEnd->baseUrl.'/img/warning1.png'?>" />-->
					  	<div class="check_Popup_Capability">
						<!--<i class="zmdi " data-dismiss="modal" aria-hidden="true" ></i>-->
						<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
						<i class="fa fa-times" data-dismiss="modal" aria-hidden="true"></i>
						<p class="capability_3">Sorry</p>
						<p class="capability_1">You are not able to complete your own capabilty test.</p>
						<p class="capability_4">Please refer to your assigned coach or assessor to complete this step.</p>
						<button class="capability_2 " data-dismiss="modal" aria-hidden="true" >Go Back</button>
    				 	 </div> 		 					  
					
    				  
					</div>
				  </div>
				  
				</div>
			</div>
		
		<!-- Modal -->
   </div>	
	<script>
	
		function popUpNotAllowed(){
			//$(".modal-body").html("Sorry, you may only access this capability test if you are an approved assessor. Please contact your assessor or manager to complete this step!");
			//$("#myModal").modal("show");
			var role = "<?=\Yii::$app->user->identity->roleName?>";
			console.log(role);
			if(role=="user")
			{
				//alert("Sorry, you may only access this capability test if you are an approved assessor. Please contact your assessor or manager to complete this step!");
				$("#myModal4").modal("show");
			}
			else{
				
				//alert("Sorry, you're not able to complete your own capability test!");
				$("#myModal4").modal("show");
			}
		}
		
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
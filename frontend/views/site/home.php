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

<!--<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->
    <!--<link rel="stylesheet" href="http://zavoloklom.github.io/material-design-iconic-font/css/docs.md-iconic-font.min.css">-->
    
	<div class="mdl-section-check">
    <div class="mdl-grid mdl-home ">
					<div class="mdl-cell mdl-cell-8-col" style="margin: 0px 32px 0px 4px !important;">
						<h1 class="mdl-sidebar"><strong>Home Page</strong></h1>
					</div>
					<div class="mdl-cell mdl-cell-4-col mdl-section" >
						<ul style="width: 1000px;padding-left: 100px;"> 
							<li style="margin-left: -7%;">
								<button class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored mdl-hover-fabelgreen mdl-icon" data-upgraded=",MaterialButton">Green</button><span class="mdl-complete">Complete</span>
								<button class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored mdl-hover-fabelyellow mdl-yellow" data-upgraded=",MaterialButton"> Amber</button><span class="mdl-complete">In Progress</span>
								<button class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored mdl-hover-fabelred mdl-darkred" data-upgraded=",MaterialButton">Red</button><span class="mdl-complete">- Not Commenced</span>
								<button class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored mdl-hover-fabelgrey mdl-lightgrey" data-upgraded=",MaterialButton">Grey</button><span class="mdl-complete">- Not applicable</span>
							</li>
						</ul>
					</div>
	</div>
		<div class="mdl-grid">
				<div class="mdl-cell mdl-cell-8-col">
					<span class="mdl-welcome"><h3>Welcome <?=\Yii::$app->user->identity->fullname?></h3></span>
					<span class="mdl-current"><h3>Current Programs :</h3></span>
				</div>
		</div>
	<?php if(Yii::$app->session->getFlash('error')!='') {?>
	<div class="alert alert-danger" role="alert">
		<strong> <?= Yii::$app->session->getFlash('error'); ?>.</strong>
	</div>
	
	<?php 
	}
	$username ='';
	
	foreach($programs as $program)
	{
		$modules = $program->publishedModules;
		// mdl-cell This class is removed from [ mdl-cell-8-col ] for Alignment into stright line - 56
		if(count($modules) > 0 && count($program->programEnrollments) > 0)
		{
		echo '<div class="mdl-grid">
			<div class="mdl-cell-8-col">
				<span class="mdl-program"><h4><span class="mdl-test">Program</span> : '.$program->title.'</h4></span>
			</div>
		</div>';
		echo '<div class="horizontal al_cpp_category_16">';
		echo '<ul class="name_list" >';		
			foreach($users as $user){
				if($user->user->isEnrolled($program->program_id)){
					$name = $user->firstname. " ". $user->lastname;
					if($name == '')
						$name = $user->user->username;
					//$progress = 0;
					$progress = $user->user->getProgramProgress($program->program_id);
					echo '
					<li><div class="mdl-grid" >
						<div class="mdl-cell mdl-cell--3-col mdl-bar" >
							<div class="mdl-card--border">
								<div class="w3-progress-container">
									<div class="w3-progressbar" style="width:'.$progress.'%">'.$progress.'%</div><span class="mdl-label">'.$name.'</span></div>
								</div>
							</div>
						</div>
					</li>';
				}
			}
	
		echo '</ul>';
        echo'<div class="all_course al_pragram_width ">';
		foreach($modules as $p_key=>$module)
		{
			$units = $module->publishedUnits;
			if(count($units) > 0)
			{
			//echo $p_key;
			if($p_key == 0)
				echo '<div class="course_listing al_single_course_width units-present-4">';
			else 
				echo '<div class="course_listing al_single_course_width units-present-4" >'
			;
					echo '<div class="course_name" style="position:relative;">
                            <h2>
                                '.$module->title.'
                            </h2>
                    </div>
					<div class="course_units">
                        <ul>';

				foreach($units as $k=>$unit){
					if($k==0)
							echo "<li>";
					else 
						echo '<li class="margin" style="margin-left: -298px">';
						echo 
							'<div class="single_unit_title" id="'.$unit->unit_id.'" style="overflow:visible; white-space:initial;" ><a href="#'.$unit->title.'">
                                        '.$unit->title.'
                            </a></div>
							<div class="course_types">';
							foreach($users as $key => $user){
								if($user->user->isEnrolled($program->program_id))
								{
									echo ' <div class="course_indicate">
												<div class="assessement_item">
													<div name="unit1">';
													if(!$key)
														echo '<span class="first_heading">Aware</span>';
													else echo '<span class="first_heading" style="display: none">Aware</span>';
													$progress = $user->user->getUnitProgress($unit->unit_id);
													if($progress['ap'] == "red")
														$action = "learn";
													else $action = "learn";
													$url = Url::to(["test/$action",'u_id'=>$unit->unit_id]);
													echo "<div name='unit1'>
															<a class='mdl-button mdl-js-button mdl-button--fab mdl-hover-{$progress['ap']} mdl-small-icon-{$progress['ap']}' href='$url'><span class='toolkit'><center>{$progress['ap']}</center></span>
															</a>
														</div>

													</div>

													<div name='unit1'>";
														//if(!$key)
															echo "<span class='first_heading'>Capable</span>";
														//else echo '<span class="first_heading" style="display: none">Capable</span>';
														$href= 'javascript:void(0);';
														if($progress['cp'] != "grey")
															$onClick = 'popUpNotAllowed();';
														else $onClick = '';
														echo "<div name='unit1'>
															<a class='mdl-button mdl-js-button mdl-button--fab mdl-hover-{$progress['cp']} mdl-small-icon-{$progress['cp']}' href=".$href." onClick={$onClick }><span class='toolkit'><center>{$progress['cp']}</center></span>
															</a>

														</div>
													</div>


												</div>
											</div>";
								}//if enrolled
							}
								
					echo "</div></li>";
					//$i++;
				}		
			echo "</ul></div>";
		echo "</div>";
			} //if unit count
		}
		echo "</div></div>";
		?>


		<?php
		} //module count && enrollment count
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

	</script>
	<style>
	.modal-backdrop{
		z-index:0 !important
	}

	</style>


<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\models\SiteMeta;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
	<?php if($this->title == 'Reports') { ?>
	<link rel="stylesheet" href="<?=Yii::$app->homeUrl;?>css/custom/buttons.min.css">
	<?php } ?>
    <?php $this->head() ?>

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="<?=Yii::$app->homeUrl;?>js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="<?=Yii::$app->homeUrl;?>js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->

</head>
	<body class="menubar-hoverable header-fixed menubar-pin ">
	<?php $this->beginBody() ?>
		<!-- BEGIN HEADER-->
		<header id="header" >
			<div class="headerbar">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="headerbar-left">
					<ul class="header-nav header-nav-options">
						<li class="header-nav-brand" >
							<div class="brand-holder">
								<a href="<?=\Yii::$app->homeUrl;?>">
								<?php
								$right_logo = SiteMeta::find()->where(['meta_key'=>'left-side-logo'])->one();
								?>
									<img src="<?=Yii::$app->urlManagerBackEnd->baseUrl.'/'.$right_logo->meta_value;?>" />
								</a>
							</div>
						</li>
						<li>
							<a class="btn btn-icon-toggle menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
								<i class="fa fa-bars"></i>
							</a>
						</li>
					</ul>
				</div>
				<!-- Collect the nav links, forms, and other content for toggling -->
				<?php if(!\Yii::$app->user->isGuest){ ?>
				<div class="headerbar-right">

					<ul class="header-nav header-nav-options">
						<li class="header-nav-brand" >
							<div class="brand-holder">
								<a href="<?=\Yii::$app->homeUrl;?>">
									<?php if(!\Yii::$app->user->isGuest && !\Yii::$app->user->can('superadmin')){ 
										$company = common\models\Company::findOne(\Yii::$app->user->identity->c_id);
										if($company){
											if((file_exists(Yii::getAlias('@backend').'/web/'.$company->logo)) && (!empty($company->logo))){ 										
							?>										
								<img src="<?= Yii::$app->urlManagerBackEnd->baseUrl."/".$company->logo;?>" />
								<?php } else { ?>
								<img  src="<?=Yii::$app->urlManagerBackEnd->createAbsoluteUrl(['img/default_logo.jpg'])?>"/> 
										<?php } ?>
									<?php }} else{ 
										$left_logo = SiteMeta::find()->where(['meta_key'=>'right-side-logo'])->one();
										?>
										<img src="<?=Yii::$app->urlManagerBackEnd->baseUrl.'/'.$left_logo->meta_value;?>" />
									<?php }?>
								</a>
							</div>
						</li>

					</ul>
					<ul class="header-nav header-nav-toggle">
						<li>
							<a class="btn btn-icon-toggle btn-default" href="#offcanvas-search" data-toggle="offcanvas" data-backdrop="false">
								<i class="fa fa-ellipsis-v"></i>
							</a>
						</li>
					</ul><!--end .header-nav-toggle -->
				</div><!--end #header-navbar-collapse -->
				<?php } ?>
			</div>
		</header>
		<!-- END HEADER-->

		<!-- BEGIN BASE-->
		<div id="base-full">

			<!-- BEGIN OFFCANVAS LEFT -->
			<div class="offcanvas">
			</div><!--end .offcanvas-->
			<!-- END OFFCANVAS LEFT -->

			<!-- BEGIN CONTENT-->
			<div id="content">
				<section>
					<div class="section-body contain-lg">
						<?= $content ?>
					</div>
				</section>
			</div><!--end #content-->
			<!-- END CONTENT -->
		</div>
		<!-- END BASE -->
		
		<?php if(!\Yii::$app->user->isGuest ){ ?>
			<div class="offcanvas">

				<!-- BEGIN OFFCANVAS SEARCH -->
				<div id="offcanvas-search" class="offcanvas-pane width-6">
					<div class="offcanvas-head">
						<span class="text-medium">									
							<small><?php echo Yii::$app->user->identity->fullname;?></small>
							<small>[ <?= $user_role_name = Yii::$app->user->identity->role;?> ]</small>
						</span>
						<div class="offcanvas-tools">
							<a class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
								<i class="md md-close"></i>
							</a>
						</div>
					</div>
					<div class="offcanvas-body no-padding">
					
					<div class="small-padding">
					<?php if((\Yii::$app->user->can("company_assessor")) || (\Yii::$app->user->can("group_assessor")) || (\Yii::$app->user->can("local_assessor")) ) {?>
					<a href="<?=Yii::$app->urlManagerBackEnd->baseUrl?>">DASHBOARD</a>
					<?php } ?>
					<?= Html::beginForm(['/site/logout'], 'post')
										. Html::submitButton(
											'Logout',
											['class' => 'btn btn-link logout']
										)
										. Html::endForm()
										?> </div>
					</div>
				</div>
				
			</div>
		<?php } ?>
	</body>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
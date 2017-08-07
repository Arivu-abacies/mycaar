<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use backend\assets\AppAsset;

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
    <?php $this->head() ?>

		<!-- BEGIN STYLESHEETS -->
		<link href='http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
		<link type="text/css" rel="stylesheet" href="<?=\Yii::$app->homeUrl;?>css/theme-default/bootstrap.css?1422792965" />
		<link type="text/css" rel="stylesheet" href="<?=\Yii::$app->homeUrl;?>css/theme-default/materialadmin.css?1425466319" />
		<link type="text/css" rel="stylesheet" href="<?=\Yii::$app->homeUrl;?>css/theme-default/font-awesome.min.css?1422529194" />
		<link type="text/css" rel="stylesheet" href="<?=\Yii::$app->homeUrl;?>css/theme-default/material-design-iconic-font.min.css" />	
		
		<!-- END STYLESHEETS -->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="../../assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="../../assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->

</head>
	<body class="menubar-hoverable header-fixed ">
<?php $this->beginBody() ?>

	
	<!-- BEGIN LOGIN SECTION -->
	<section class="section-account">
		<div class="img-backdrop" style="background-image: url('<?=Yii::$app->homeUrl?>img/img16.jpg')"></div>
		<div class="spacer"></div>
		<div class="card contain-sm style-transparent">
			<div class="card-body">
				<div class="row">
					
						<br/>
<!--						<span class="text-lg text-bold text-primary">MATERIAL ADMIN</span>
						<br/><br/>
						<form class="form floating-label" action="http://www.codecovers.eu/materialadmin/dashboards/dashboard" accept-charset="utf-8" method="post">
							<div class="form-group">
								<input type="text" class="form-control" id="username" name="username">
								<label for="username">Username</label>
							</div>
							<div class="form-group">
								<input type="password" class="form-control" id="password" name="password">
								<label for="password">Password</label>
								<p class="help-block"><a href="#">Forgotten?</a></p>
							</div>
							<br/>
							<div class="row">
								<div class="col-xs-6 text-left">
									<div class="checkbox checkbox-inline checkbox-styled">
										<label>
											<input type="checkbox"> <span>Remember me</span>
										</label>
									</div>
								</div><!--end .col -->
<!--								<div class="col-xs-6 text-right">
									<button class="btn btn-primary btn-raised" type="submit">Login</button>
								</div><!--end .col -->
<!--							</div><!--end .row -->
<!--						</form>-->
<!--					</div><!--end .col -->
<!--end .col -->
					
				<!--end .row -->
				<?=$content?>
			</div><!--end .card-body -->
		</div><!--end .card -->
	</section>
	<!-- END LOGIN SECTION -->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

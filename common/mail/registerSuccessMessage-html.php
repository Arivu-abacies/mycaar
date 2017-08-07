<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */
$loginLink = Yii::$app->urlManager->createAbsoluteUrl(['site/login']);
?>

Hello <?= $user->username ?>,

<br>Hi, Welcome to Mycaar.
<br>Registered to Mycaar application is successfully done!!!.
	
<br>
Thanks, 
<br>
<?= $loginLink ?>


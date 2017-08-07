<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */
$loginLink = Yii::$app->urlManagerFrontEnd->createAbsoluteUrl(['site/login']);
?>

Hello <?= $user->username ?>,

<br>Your Password is changed Successfully!!!.
<br>
	
<br>
Thanks, 
<br>
<?= $loginLink ?>


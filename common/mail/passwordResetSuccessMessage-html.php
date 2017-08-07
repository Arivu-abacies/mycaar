<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */
$loginLink = Yii::$app->urlManager->createAbsoluteUrl(['site/login']);
?>

Hello <?= $user->username ?>,

<br>Your Password is Reseted Successfully!!!.

	
<br>
Thanks, 
<br>
<?= $loginLink ?>


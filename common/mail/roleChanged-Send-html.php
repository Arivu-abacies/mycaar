<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */

$loginLink = Yii::$app->urlManagerFrontEnd->createAbsoluteUrl(['site/login']);


?>

Hello <?= $user->email ?>,


<br>Your Access Level has Been Changed. Please Login and Verify.

	
<br>
Thanks, 
<br>
<?= $loginLink ?>



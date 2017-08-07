<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */

$loginLink = Yii::$app->urlManager->createAbsoluteUrl(['site/login']);
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);


?>

Hello <?= $user->email ?>,

<br>Your Username/Email ID: <?= $user->email ?>
<br>Your Password: <?= $password ?>

<br>After login, Please Kindly Delete this Message for security.


<br>Follow the link below to reset your password:
<br><?= Html::a(Html::encode($resetLink), $resetLink) ?>
	
<br>
Thanks, 
<br>
<?= $loginLink ?>


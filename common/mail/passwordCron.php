<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

Hello <?= $username ?>,

<br>Your Username/Email ID: <?= $username ?>
<br>Your Password: <?= $password ?>

<br>After login, Please Kindly Delete this Message for security.


<br>Follow the link below to reset your password:
<br><?= Html::a(Html::encode($resetLink), $resetLink) ?>
	
<br>
Thanks, 
<br>
<?= $loginLink ?>


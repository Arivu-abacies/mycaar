<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */
$loginLink = Yii::$app->urlManagerFrontEnd->createAbsoluteUrl(['site/login']);
?>

Hello <?= $user->username ?>,

<br>Congratulations! You have enrolled in Program "<?= $programname ?>" successfully!

<br>You may check all Program you are enrolled in here:<?= Html::a('Dashboard',$loginLink) ?>.

<br>Yours sincerely,
<br>MyCaar Teams.



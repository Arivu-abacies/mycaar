<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserProfile */

$this->title = 'Update User Profile: ' . $model->profile_id;
$this->params['breadcrumbs'][] = ['label' => 'User Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->profile_id, 'url' => ['view', 'id' => $model->profile_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-profile-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

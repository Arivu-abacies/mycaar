<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Module */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Modules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->module_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->module_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'module_id',
            'program_id',
            'title',
            'short_description:ntext',
            'featured_video_url:ntext',
            'detailed_description:ntext',
            'status',
        ],
    ]) ?>

</div>

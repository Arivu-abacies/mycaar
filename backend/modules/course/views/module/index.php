<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchModule */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Modules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Module', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'module_id',
            'program.title',
            'title',
            //'short_description:ntext',
            //'featured_video_url:ntext',
            // 'detailed_description:ntext',
            // 'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

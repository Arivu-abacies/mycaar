<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\Company */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Edit', ['update', 'id' => $model->company_id], ['class' => 'btn btn-primary']) ?>
		<?php if(Yii::$app->user->can('company manage')){ ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->company_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
		
		 <?= Html::a('All Companies', ['index'], ['class' => 'btn btn-success']) ?>
		<?php } ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'about_us:ntext',
		
			[
                'attribute'=>'Logo',
				'value'=>!empty($model->logo)?Yii::$app->homeUrl.$model->logo:'',
				 'format' => !empty($model->logo)?['image',['height'=>'100px','width'=>'300px']]:'text',
				
            ],	
				'companyAdmin.email',				
			[
                'attribute'=>'slug',					
				'value'=>Yii::$app->urlManagerFrontEnd->createAbsoluteUrl(['site/signup/'.$model->slug]),					
            ],	
				
        ],
    ]) ?>

</div>

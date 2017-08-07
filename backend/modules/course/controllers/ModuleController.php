<?php

namespace backend\modules\course\controllers;

use Yii;
use common\models\Module;
use common\models\Program;
use common\models\search\SearchModule;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\helpers\Url;

/**
 * ModuleController implements the CRUD actions for Module model.
 */
class ModuleController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
             'access' => [
                'class' => AccessControl::className(),
				'only' => ['create','delete'],
                'rules' => [
                    [
                        'actions' => ['create','delete'],
                        'allow' => true,
						'roles' => ['company_admin']
                    ],
                ],
            ], 
        ];
    }



    /**
     * Creates a new Module model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($p_id=null)
    {
		$model = new Module();
		$program = false;
		$model->language = "English";
		if($p_id){
			$program = Program::findOne($p_id);
			if($program == null)
				throw new NotFoundHttpException('The requested page does not exist.');
			$model->program_id = $p_id;
			$disabled = true;
		}      
		else $disabled = false;
        if ($model->load(Yii::$app->request->post())) {
			//save image here
			//print_r(Yii::$app->request->post());die;
			$model->featured_image = UploadedFile::getInstance($model, 'featured_image');
			if(!empty($model->featured_image)) {
				if(!$model->uploadImage())
					return;
			}
			if($model->save())
            return $this->redirect(['update', 'id' => $model->module_id]);
        } else {
            return $this->render('add', [
                'model' => $model,
				'disabled' => $disabled,
				'program' => $program
            ]);
        }
    }

    /**
     * Updates an existing Module model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		$disabled = false;
        $model = $this->findModel($id);
		$program = $model->program;
		if (\Yii::$app->user->can('manageProgram', ['post' => $program])) {
			$current_image = $model->featured_image;
			//$current_video = $model->featured_video_url;
			if ($model->load(Yii::$app->request->post())) {
				
				//Save featured image here
				$model->featured_image = UploadedFile::getInstance($model, 'featured_image');
				if(!empty($model->featured_image)) {
					if(!$model->uploadImage())
						return;
				}
				else
					$model->featured_image = $current_image;
				//end of saving image
				
				//save featured video
			/* 	$model->featured_video_url = UploadedFile::getInstance($model, 'featured_video_url');
				if(!empty($model->featured_video_url)) {
					if(!$model->uploadVideo())
						return;
				}
				else
					$model->featured_video_url = $current_video;  */
				//end of saving video
				
				if($model->save())
					return $this->redirect(['update', 'id' => $model->module_id]);
			} else {
				return $this->render('add', [
					'model' => $model,
					'disabled' => $disabled,
					'program' => $program
				]);
			}
		}else{
					//yii\web\ForbiddenHttpException
					throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
				}
    }

    /**
     * Deletes an existing Module model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
		return $this->redirect(['program/program-list']);
        //return $this->redirect(['index']);
    }

    /**
     * Finds the Module model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Module the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Module::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	public function actionReOrder(){
		$data = \Yii::$app->request->post()['data'];
		//print_r($data);die;
		foreach($data as $order=>$module){
			$module = $this->findModel($module['id']);
			$module->module_order = $order;
			$module->save();
		}
		return true;
	}

		public function actionUpload(){
		//$name = uniqid();
		$name = preg_replace( 
                     array("/\s+/", "/[^-\.\w]+/"), 
                     array("_", ""), 
                     trim($_FILES["media"]["name"])); 
		$dir = "uploads/media/";
		move_uploaded_file($_FILES["media"]["tmp_name"], $dir. $name);
		return $url = Yii::$app->urlManager->createAbsoluteUrl([$dir.$name]);
	}
	
}

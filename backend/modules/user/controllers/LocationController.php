<?php

namespace backend\modules\user\controllers;

use Yii;
use common\models\Location;
use common\models\search\SearchLocation;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LocationController implements the CRUD actions for Location model.
 */
class LocationController extends Controller
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
        ];
    }

    /**
     * Lists all Location models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchLocation();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Location model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Location model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Location();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->location_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Location model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->location_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Location model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Location model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Location the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Location::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	/**
     * Multiple Deletes an existing Division model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
	 
	 public function actionMultiDelete()
    {    
		$location_id = Yii::$app->request->post()['location_id'];
		if($location_id)
		{
			 foreach($location_id as $tmp)
			  $this->findModel($tmp)->delete(); 
		} 
			
    }
	
	public function actionGetLocation($c_id){
		$mods = Location::find()->where(['company_id'=>$c_id])->orderBy('name')->all();
		 if(count($mods)>0){
			echo "<option value=''>--Select--</option>";
			foreach($mods as $mod){			
				  echo "<option value='".$mod->location_id."'>".$mod->name."</option>";
			}
		}
		else{
			echo "<option value=''>-</option>";
		}
	}
	
	
	public function actionGroupLocation($id){
		
		if($id == "group_assessor")
		{
			$cid = Yii::$app->user->identity->c_id;
			if($cid)
			{
				$mods = Location::find()->where(['company_id'=>$cid])->orderBy('name')->all();
				if($mods)
				{
				  foreach($mods as $mod){		
					echo "<input type='checkbox' name='UserProfile[access_location][]' value='".$mod->location_id."'> ".$mod->name."<br>";
				  }
				}
			} else {
				echo "Invalid Company Name";
			}
		}	
	}
	
	public function actionUpdateGroupLocation($locations,$id){
		$accesslocation = explode(",",$locations);
		 if($id == "group_assessor")
		{
			$cid = Yii::$app->user->identity->c_id;			
			if($cid)
			{
				$mods = Location::find()->where(['company_id'=>$cid])->orderBy('name')->all();
				if($mods)
				{
				  foreach($mods as $mod){	
					if (in_array($mod->location_id, $accesslocation))
					{
					echo "<input type='checkbox' checked='checked' name='UserProfile[access_location][]' value='".$mod->location_id."'> ".$mod->name."<br>";
					} else 
					{
					echo "<input type='checkbox' name='UserProfile[access_location][]' value='".$mod->location_id."'> ".$mod->name."<br>";
					}	
				  }
				}
			} else {
				echo "Invalid Company Name";
			}
		}	 
	}
	
}

<?php

namespace backend\modules\user\controllers;

use Yii;
use common\models\Location;
use common\models\User;
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
			   if((Yii::$app->user->can('group_assessor')) && (!Yii::$app->user->can('company_admin'))) { 
					//$setlocation = \Yii::$app->user->identity->userProfile->access_location;	
					$users_details = User::findOne(\Yii::$app->user->id);
					$setlocation = $users_details->userProfile->access_location;					
					if($setlocation)
					{
						$setlocation = explode(",",$setlocation);
					}
				
					$mods = Location::find()->where(['company_id'=>$cid])->andWhere(["in","location_id",$setlocation])->orderBy('name')->all();
				}else{
					$mods = Location::find()->where(['company_id'=>$cid])->orderBy('name')->all();
				}
				if($mods)
				{
				 echo "<label>Select Locations for This Group Accessor</label>";
				 echo "<div class='group-accessor'>";
				  foreach($mods as $mod){					  
					echo "<div class='col-md-6'><input type='checkbox' name='UserProfile[access_location][]' value='".$mod->location_id."'> ".$mod->name."</div>";
				  }
				 echo "</div>";
				}
				else {
					echo "Invalid Company Name, Please Check";
				}
			} else {
				echo "Invalid Company Name";
			}
		}	
	}
	
	public function actionUpdateGroupLocation($locations,$userid,$id){
		$accesslocation = explode(",",$locations);
		$userlocation = "";
		$user = User::find()->where(['id'=>$userid])->One();
		$userlocation = $user->userProfile->location;
		 if($id == "group_assessor")
		{
			$cid = Yii::$app->user->identity->c_id;			
			if($cid)
			{
				 if((Yii::$app->user->can('group_assessor')) && (!Yii::$app->user->can('company_admin'))) { 
					//$setlocation = \Yii::$app->user->identity->userProfile->access_location;
					$users_details = User::findOne(\Yii::$app->user->id);
					$setlocation = $users_details->userProfile->access_location;
					if($setlocation)
					{
						$setlocation = explode(",",$setlocation);
					}
				
					$mods = Location::find()->where(['company_id'=>$cid])->andWhere(["in","location_id",$setlocation])->orderBy('name')->all();
				}else{
					$mods = Location::find()->where(['company_id'=>$cid])->orderBy('name')->all();
				}
				
				if($mods)
				{
				 echo "<label>Select Locations for This Group Accessor</label>";
				 echo "<div class='group-accessor'>";
				  foreach($mods as $mod){	
					if (in_array($mod->location_id, $accesslocation))
					{
						
						$default_location =($userlocation == $mod->location_id )?"disabled='disabled'":"";
						if(empty($default_location))
							$default_location =(!Yii::$app->user->can('company_admin'))?"disabled='disabled'":"";
						
					echo "<div class='col-md-6'><input type='checkbox' ".$default_location." checked='checked' name='UserProfile[access_location][]' value='".$mod->location_id."'> ".$mod->name."</div>";
					} else 
					{
						$default_location =(!Yii::$app->user->can('company_admin'))?"disabled='disabled'":"";
						
					echo "<div class='col-md-6'><input type='checkbox' ".$default_location." name='UserProfile[access_location][]' value='".$mod->location_id."'> ".$mod->name."</div>";
					}	
				  }
				   echo "</div>";
				}
				else {
					echo "Invalid Company Name, Please Check";
				}
			} else {
				echo "Invalid Company Name";
			}
		}	 
	}
	
}


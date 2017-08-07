<?php

namespace backend\modules\course\controllers;

use Yii;
use common\models\Program;
use common\models\Company;
use common\models\ProgramEnrollment;
use common\models\search\SearchProgram;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\search\SearchModule;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
/**
 * ProgramController implements the CRUD actions for Program model.
 */
class ProgramController extends Controller
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
				'only' => ['index','create','view','dashboard','delete','company-programs'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
						'roles' => ['superadmin']
                    ],
                    [
                        'actions' => ['create','view','delete','company-programs'],
                        'allow' => true,
						'roles' => ['company_admin']
                    ],
                    [
                        'actions' => ['dashboard'],
                        'allow' => true,
						//'roles' => ['assessor']
						'roles' => ['company_assessor','group_assessor','local_assessor']
                    ],
                ],
            ], 
        ];
    }
	public function actionProgramList(){
        $searchModel = new SearchProgram();
		if (\Yii::$app->user->can('superadmin')) {
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		}
        else $dataProvider = $searchModel->searchCompanyPrograms(Yii::$app->request->queryParams);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);		
	}
    /**
     * Lists all Program models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchProgram();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	public function actionDashboard(){	 
	
	if(Yii::$app->user->can('superadmin')){ 
			return $this->render('dashboard_system');	 
	}
	  if(!Yii::$app->user->can('superadmin')){ 
			$company = Company::find()->where(['company_id'=>\Yii::$app->user->identity->c_id ])->one();	 
			
			if($company && $company->status == 1)
			   return $this->render('under_construction', [ 'company' => $company] );
			}
		
		
			$programs = Program::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->orderBy('title')->all();
			$query = ProgramEnrollment::find()->orderBy('user_profile.firstname ASC');
			$query->innerJoinWith(['userProfile as user_profile']);
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
				 'pagination'=>false,
			]);	
			$query->innerJoinWith(['user']);
			$query->andFilterWhere(['user.c_id'=>\Yii::$app->user->identity->c_id]);
			
			 if(Yii::$app->user->can("group_assessor")){		
				$setlocation = \Yii::$app->user->identity->userProfile->access_location;			  
				$query->andFilterWhere(['in', 'location', $setlocation]);
			  }
			  else if(Yii::$app->user->can("local_assessor")){	
				$query->andFilterWhere(['location'=>\Yii::$app->user->identity->userProfile->location]);
			  }
			  
			$query->groupBy('program_enrollment.user_id');
			$users = $dataProvider->models;			
			
			return $this->render('dashboard', [
						'programs' => $programs,
						'users' => $users,
						'params' => false,
					]);	
					
		
		//return $this->render('dashboard');	 
	}
	
	
	/* public function actionDashboard(){
	 if(\Yii::$app->user->can('superadmin')){ 
		$companys = Company::find()->orderBy('name')->all();
		return $this->render('dashboard_admin', [
			'companys' => $companys,
		]);
	 }  else {	
		$programs = Program::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->orderBy('title')->all();
		return $this->render('dashboard', [
			'programs' => $programs,
		]);
	 }
	} */
    /**
     * Lists all Program models.
     * @return mixed
     */
    public function actionCompanyPrograms()
    {
		if (\Yii::$app->user->can('superadmin')) {
			return $this->redirect(['index']);
		}
/*         $searchModel = new SearchProgram();
        $dataProvider = $searchModel->searchCompanyPrograms(Yii::$app->request->queryParams);

        return $this->render('company_index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]); */
        $searchModel = new SearchProgram();
        $dataProvider = $searchModel->searchCompanyPrograms(Yii::$app->request->queryParams);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
    /**
     * Displays a single Program model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		$model = $this->findModel($id);
		if (\Yii::$app->user->can('manageProgram', ['post' => $model])) {
			$searchModel = new SearchModule();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams,$id);
			return $this->render('view', [
				'model' => $this->findModel($id),
				'searchModel' => $model,
				'dataProvider' => $dataProvider,
			]);
		}else{
			//yii\web\ForbiddenHttpException
			throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
		}
    }

    /**
     * Creates a new Program model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Program();

        if ($model->load(Yii::$app->request->post())  && $model->save() ) {

			$model->save();
			if (\Yii::$app->user->can('superadmin')) {
					return $this->redirect(['index']);
			}
            return $this->redirect(['program-list']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Program model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		
        $model = $this->findModel($id);
		if (\Yii::$app->user->can('manageProgram', ['post' => $model])) {
			if ($model->load(Yii::$app->request->post()) && $model->save()) {
				return $this->redirect(['view', 'id' => $model->program_id]);
			} else {
				return $this->render('update', [
					'model' => $model,
				]);
			}
		}else{
			//yii\web\ForbiddenHttpException
			throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
		}
    }

    /**
     * Deletes an existing Program model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$model = $this->findModel($id);
		if (\Yii::$app->user->can('manageProgram', ['post' => $model])) {
			//$this->findModel($id)->deleteProgram();
			$this->findModel($id)->delete();
			if (\Yii::$app->user->can('super_admin'))
				return $this->redirect(['index']);
			else return $this->redirect(['program-list']);
		}else{
			//yii\web\ForbiddenHttpException
			throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
		}
    }

    /**
     * Finds the Program model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Program the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Program::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


	public function actionGetProgram($c_id){
		$mods = Program::find()->where(['company_id'=>$c_id])->orderBy('title')->all();
		 if(count($mods)>0){
			echo "<option value=''>--Select--</option>";
			foreach($mods as $mod){			
				  echo "<option value='".$mod->program_id."'>".$mod->title."</option>";
			}
		}
		else{
			echo "<option value=''>-</option>";
		}
	}

}

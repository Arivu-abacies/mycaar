<?php

namespace backend\modules\course\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

use common\models\Report;
use common\models\search\SearchReport;
use common\models\Program;
use common\models\search\SearchProgram;
use common\models\ProgramEnrollment;
use common\models\UserProfile;

use common\models\Module;
use common\models\search\SearchModule;

use common\models\Unit;
use common\models\search\SearchUnit;

use common\models\UnitReport;
use common\models\search\SearchUnitReport;

use \moonland\phpexcel\Excel;
/**
 * UnitController implements the CRUD actions for Unit model.
 */
class ReportController extends Controller
{
     /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [

             'access' => [
                'class' => AccessControl::className(),
				'only' => ['search','reset-programs','reset-modules','reset-units','reset-users'],
                'rules' => [
                    [
                        'actions' => ['search','assessor-report'],
                        'allow' => true,
						//'roles' => ['assessor']
						'roles' => ['company_assessor','group_assessor','local_assessor']
                    ],
                    [
                        'actions' => ['reset-programs','reset-modules','reset-units','reset-users'],
                        'allow' => true,
						'roles' => ['company_admin']
                    ],
                ],
            ], 
        ];
    }   

	public function actionSearch($p_id=null,$data=null){
		
		$programs = $users = [];
		$param = false;
		if(\Yii::$app->request->post())
			$param = \Yii::$app->request->post();
		else if($data)
			$param = unserialize($data);

		if($param)
		{
			if(isset($param['company']))
				$company_id = $param['company'];
			else
				$company_id = \Yii::$app->user->identity->c_id;
		}	
		
		if($param){			
				
			//find program
			if(isset($param['program']) && $param['program'] !=''){				
				$programs[] = Program::find()->where(['program_id'=>$param['program']])->one();
			}else{
				$programs = Program::find()->where(['company_id'=>$company_id])->orderBy('title')->all();
			}
			//$query = ProgramEnrollment::
			$query = ProgramEnrollment::find()->orderBy('user_profile.firstname ASC');
			
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
					 'pagination' => [
						'pageSize' => 50,
						 'page' =>$param['page'], 
					], 
			]);	
		
		$dataProvider2 = new ActiveDataProvider([
				'query' => $query,
				'pagination' => [
						'pageSize' => 0,
						 
					], 				
			]);	
			
			$query->innerJoinWith(['userProfile as user_profile']);
			$query->innerJoinWith(['user']);
			$query->andFilterWhere(['user.c_id'=>$company_id]);			
			//if any of the user parametr is filled,then search for that users
			//$query = User::find()->where(['c_id' =>Yii::$app->user->identity->c_id]);
			if(isset($param['program']) && $param['program'] !='')
				$query->andFilterWhere(['program_id'=>$param['program']]);	
 			if(isset($param['user']) && $param['user'] !='')
				$query->andFilterWhere(['user_id'=>$param['user']]);			
 			if(isset($param['state']) && $param['state'] !='')
				$query->andFilterWhere(['state'=>$param['state']]);
			if(isset($param['role']) && $param['role'] !='')
				$query->andFilterWhere(['role'=>$param['role']]);
			if(isset($param['location']) && $param['location'] !='')
				$query->andFilterWhere(['location'=>$param['location']]);
			else 
			{
				 if(!Yii::$app->user->can('superadmin')){ 
				  if(Yii::$app->user->can("group_assessor")){		
					$setlocation = \Yii::$app->user->identity->userProfile->access_location;			  
					$query->andFilterWhere(['in', 'location', $setlocation]);
				  }
				  else if(Yii::$app->user->can("local_assessor")){	
					$query->andFilterWhere(['location'=>\Yii::$app->user->identity->userProfile->location]);
				  }
				 }
			}
			if(isset($param['division']) && $param['division'] !='')
				$query->andFilterWhere(['division'=>$param['division']]); 
			if(isset($param['firstname']) && $param['firstname'] !='')
				$query->andFilterWhere(['like', 'firstname',$param['firstname']]);
			if(isset($param['lastname']) && $param['lastname'] !='')
				$query->andFilterWhere(['like', 'lastname', $param['lastname']]);	
			$query->groupBy('program_enrollment.user_id');		
			$users = $dataProvider->models;
			$userscount = $dataProvider2->models;
					
			//$users = array_slice( $users, 1, 2 ); 			
				return $this->render('report', [
					'programs' => $programs,
					'users' => $users,
					'params' => $param,
					'usersfiltercount' => $userscount
				]);
			
		} else {
			
		 if($p_id)
			$programs[] = Program::find()->where(['program_id'=>$p_id])->one();
		 else $programs = Program::find()->where(['company_id'=>\Yii::$app->user->identity->c_id])->orderBy('title')->all();
			$query = ProgramEnrollment::find()->orderBy('user_profile.firstname ASC');
			$query->innerJoinWith(['userProfile as user_profile']);
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
					'pagination' => [
						'pageSize' =>50,
						 'page' =>0,
					],
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
			
			return $this->render('report', [
						'programs' => $programs,
						'users' => $users,
						'params' => false,
					]);	
			
					
		}	
	}
	public function actionAssessorReport(){
        $searchModel = new SearchUnitReport();
        
		$extrasearch = false;
		if($get=Yii::$app->request->get())
		 {
			$extrasearch = \Yii::$app->request->get();
		 }
		 
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams,$extrasearch);
		
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'params'=>$extrasearch,	
        ]);		
	}
	public function actionResetPrograms(){
		
        $searchModel = new SearchProgram();
        $dataProvider = $searchModel->searchCompanyPrograms(Yii::$app->request->queryParams);
		if($post = \Yii::$app->request->post()){
			foreach($post['selection'] as $program){
				Program::findOne($program)->resetProgram();
			}
			return $this->redirect('reset-programs');
		}
        else return $this->render('reset_programs', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);		
	}
	public function actionResetModules($p_id=null){

		
        $searchModel = new SearchModule();
		if($p_id)
			$dataProvider = $searchModel->searchCustom(Yii::$app->request->queryParams,$p_id);
		else $dataProvider = $searchModel->searchCustom(Yii::$app->request->queryParams);
		
		if($post = \Yii::$app->request->post()){
			foreach($post['selection'] as $module){
				Module::findOne($module)->resetModule();
			}
			return $this->redirect(['reset-modules','p_id'=>$p_id]);
		}
        else return $this->render('reset_modules', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'p_id' => $p_id,
        ]);				
		
		
	}
	public function actionResetUnits($m_id=null){
		$searchModel = new SearchUnit();	
		if($m_id){
			$module = Module::findOne($m_id);
			if($module == null)
				throw new NotFoundHttpException('The requested page does not exist.');			
			$dataProvider = $searchModel->searchCustom(Yii::$app->request->queryParams,$m_id);
			$p_id = $module->program_id;
		}
			
		else {
			$dataProvider = $searchModel->searchCustom(Yii::$app->request->queryParams);
			$p_id = null;
		}
        
		
		if($post = \Yii::$app->request->post()){
			foreach($post['selection'] as $unit){
				Unit::findOne($unit)->resetUnit();
			}
			return $this->redirect(['reset-units','m_id'=>$m_id]);
		}
        else return $this->render('reset_units', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'm_id' => $m_id,
			'p_id' => $p_id,
        ]);					
	}
	public function actionResetUsers($data=null,$page=null){
		$searchModel = new SearchUnitReport();
		if($data){
			$custom_search = unserialize($data);
			
		}
		$params = false;		
		if(isset(\Yii::$app->request->get()['custom_search'])){
			$params = \Yii::$app->request->get()['custom_search'];	
			//print_r($params);die;
			$dataProvider = $searchModel->searchCustom($params);				
		}
		else if(isset($custom_search)){
			$params = $custom_search;	
			//print_r($params);die;
			$dataProvider = $searchModel->searchCustom($params);				
		}
		else 
		$dataProvider = $searchModel->searchCustom(Yii::$app->request->queryParams);
		//print_r($params);
		if(\Yii::$app->request->post() && isset(\Yii::$app->request->post()['reset_type']) ){
			//print_R(\Yii::$app->request->post()['selection']);die;
			$post = \Yii::$app->request->post();
			$post_data = \Yii::$app->request->post()['search_params'];
			if(!isset($post['selection'])){
				\Yii::$app->session->setFlash('select_some', 'Please select some reports!');
				if($post['page'] != '')
					return $this->redirect(['reset-users','data'=>$post_data,'page'=>$post['page']]);
				else return $this->redirect(['reset-users','data'=>$post_data]);				
			}
						
			$type = $post['reset_type'];
			foreach($post['selection'] as $report){
				$rep = UnitReport::findOne($report);
				if($rep != null){
					switch($type){			
						case("cp"):
							$rep->resetCpTest();
							$rep->capability_progress = NULL;
							$rep->cap_done_by = NULL;
							$rep->save();
							break;
						case("aw"):
							//delete all aware questions
							$rep->resetAwTest();			
							$rep->awareness_progress = NULL;
							$rep->save();
							break;
						case("all"):
							$rep->resetUser();
							$rep->save();
							break;
					}
					//$rep->resetUser();
					//$rep->save();
					//$rep->delete();				
				}
			}
			if($post['page'] != '')
				return $this->redirect(['reset-users','data'=>$post_data,'page'=>$post['page']]);
			else return $this->redirect(['reset-users','data'=>$post_data]);
		}
		else
			return $this->render('reset_users', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'params' => $params
		]);				
		
	}
	public function actionResetTest($type,$r_id,$params){
		
		$rep = UnitReport::findOne($r_id);
		if($rep == null)
			throw new NotFoundHttpException('The requested page does not exist.');
		switch($type){			
			case("cp"):
				$rep->resetCpTest();
				$rep->capability_progress = NULL;
				$rep->cap_done_by = NULL;
				break;
			case("aw"):
				//delete all aware questions
				$rep->resetAwTest();			
				$rep->awareness_progress = NULL;
				break;
		}
		$rep->save(false);
		return $this->redirect(['reset-users','data'=>$params]);
		//return $this->redirect(['report/reset-users','u_id'=>$rep->unit_id]);
	}
	public function actionGetModules($p_id,$m_id=""){
		$mods = Module::find()->where(['program_id'=>$p_id,'status'=>1])->orderBy('title')->all();
		 if(count($mods)>0){
			echo "<option value=''>--Select Module--</option>";
			foreach($mods as $mod){
				if(isset($m_id) && !empty($m_id) && ($m_id == $mod->module_id))
				  echo "<option selected='selected' value='".$mod->module_id."'>".$mod->title."</option>";
				else	
				  echo "<option value='".$mod->module_id."'>".$mod->title."</option>";
			}
		}
		else{
			echo "<option value=''>-</option>";
		}
	}
	
	public function actionGetUnits($m_id,$u_id=""){
		$mods = Unit::find()->where(['module_id'=>$m_id,'status'=>1])->orderBy('title')->all();
		 if(count($mods)>0){
			echo "<option value=''>--Select Lesson--</option>";
			foreach($mods as $mod){
				if(isset($u_id) && !empty($u_id) && ($u_id == $mod->unit_id))
				   echo "<option selected='selected' value='".$mod->unit_id."'>".$mod->title."</option>";
				else 
					echo "<option value='".$mod->unit_id."'>".$mod->title."</option>";
			}
		}
		else{
			echo "<option value=''>-</option>";
		}
	}
    /**
     * Finds the Unit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Unit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Unit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	
	
	

}
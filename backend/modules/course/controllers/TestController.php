<?php

namespace backend\modules\course\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\models\User;
use common\models\Unit;
use common\models\CapabilityQuestion;
use common\models\CapabilityAnswer;
use common\models\UnitReport as Report;
/**
 * UnitController implements the CRUD actions for Unit model.
 */
class TestController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
             'access' => [
                'class' => AccessControl::className(),
				'only' => ['cp-test'],
                'rules' => [
                    [
                        'actions' => ['cp-test'],
                        'allow' => true,
						//'roles' => ['assessor']
						'roles' => ['company_assessor','group_assessor','local_assessor']
                    ],
                ],
            ], 
        ];
    }	
	public function isAllowed($user_id,$unit_id){
		if($user_id == \Yii::$app->user->id)
			throw new \yii\web\ForbiddenHttpException('You are not allowed to perform your own capability test.');
		$model = $this->findModel($unit_id);
		//check if the unit belongs to the assessor
		if($model->module->program->company_id != \Yii::$app->user->identity->c_id)
			throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action');
		//check if the user belongs to the assessor		
		$student = User::findOne($user_id);
		if($student == null)
			throw new NotFoundHttpException('The requested page does not exist.');
		if($student->c_id != \Yii::$app->user->identity->c_id )
			throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action');
		
		return true;
	}
	public function actionCpTest($user_id,$unit_id, $data=null){
		
		$user = User::findOne($user_id);
		$model = $this->findModel($unit_id);
	if(!\Yii::$app->user->can("superadmin"))
	{
		/** SOME ACCESS CHECKS **/
		$this->isAllowed($user_id,$unit_id);
		/** SOME ACCESS CHECKS **/
	}	
		$session = Yii::$app->session;
		$attempted = Report::find()->where(['unit_id'=>$unit_id,'student_id'=>$user_id])->one();
		if($attempted && $attempted->capability_progress == 100 ){
			throw new \yii\web\ForbiddenHttpException('Sorry,This capability test has already been finished.You are not allowed to take this one more time');
		}
		//clear all the answers on first try if not percentage is 100
		if(!isset($session[$unit_id."_cp_".$user_id])){
			$questions = CapabilityQuestion::find()->select(['cq_id'])->where('unit_id = :unit_id', [':unit_id' => $unit_id])->asArray()->all();
			foreach($questions as $question){
				$answer = CapabilityAnswer::find()->where(['user_id'=>$user_id,'question_id'=>$question])->one();
				if($answer !== null)
					$answer->delete();
			}
			
			//clear all answers recorded
		}
		//No pagination
		//$session->remove($unit_id."_".$user_id);
		if(isset($session[$unit_id."_cp_".$user_id])){
			$page = $session[$unit_id."_cp_".$user_id];
		}			
		else $page = $session[$unit_id."_".$user_id] = 0;
		$limit = 25;
		$offset = $page*$limit;
		
		$final = false;
		$total = CapabilityQuestion::find()->where('unit_id = :unit_id', [':unit_id' => $unit_id])->count();
		if($total <= ($page+1)*$limit)
			$final = true;
		$questions = CapabilityQuestion::find()->where('unit_id = :unit_id', [':unit_id' => $unit_id])->orderBy('order_id ASC')->limit($limit)->offset($offset)->all();
		if($answers = Yii::$app->request->post()){
			//print_r($answers);die;
			if(count($answers) >1 ){
				$this->saveAnswers($answers,$user_id);
				$session[$unit_id."_cp_".$user_id] = $page+1;
				$this->saveProgress($user_id,$unit_id);				
			}

			if(isset(Yii::$app->request->post()['save_n_exit'])){
				$session->remove($unit_id."_cp_".$user_id);
				$idcheck = 	"cw_".$unit_id."_".$user_id;
				return $this->redirect(['report/search','p_id'=>$model->module->program->program_id,'data'=>$data,'#' => $idcheck]);
			}else
			return $this->redirect(["cp-test","user_id"=>$user_id,"unit_id"=>$unit_id]);
			
		}
		
		else return $this->render('test', [
            'model' => $model,
			'questions' => $questions,
			'answers' => false,
			'errors' => false,
			'final'=> $final,
			'user'=> $user
        ]);
	}
	public function saveAnswers($answers,$user_id){
		foreach($answers as $question=>$answer){				
			$cp_answer = CapabilityAnswer::find()->where(['user_id'=>$user_id,'question_id'=>$question])->one();
			if(!$cp_answer)
				$cp_answer = new CapabilityAnswer();
			
			$cp_answer->user_id = $user_id;
			$cp_answer->question_id = $question;			
			$cp_answer->answer = $answer;
			$cp_answer->save();				
		}		
	}
	public function saveProgress($user_id,$unit_id){
		$query = new \yii\db\Query;
		$query->select('count(question.cq_id) as questions, count(answer.ca_id) as right_answer')
				->from('capability_question question')
				->leftJoin('capability_answer answer', 'answer.question_id = question.cq_id AND answer.answer = question.answer AND answer.user_id='.$user_id)  
				->where("question.unit_id=$unit_id");
		$command = $query->createCommand();
		$resp = $command->queryOne();
		//print_R($resp);die;
		$progress = ($resp['right_answer']/$resp['questions'])*100;//die;
		//save progress to DB
		$report = Report::find()->where(['unit_id'=>$unit_id,'student_id'=>$user_id])->one();
		if(!$report)
			$report = new Report();
		$report->unit_id = $unit_id;
		$report->student_id = $user_id;
		$report->cap_done_by = \Yii::$app->user->id;
		$report->capability_progress = $progress;
		$report->updated_at = date('Y-m-d H:i:s');
		$report->save(false);
		return (int)$progress;
		//print_R($resp);
		//get total answered by the user and validate the right ones
		
		//progress is right/total*100
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
<?php

namespace common\models;

use Yii;
use common\models\CapabilityQuestion;
use common\models\UnitReport;

/**
 * This is the model class for table "program".
 *
 * @property integer $program_id
 * @property string $title
 * @property integer $company_id
 * @property string $description
 *
 * @property Module[] $modules
 * @property ProgramEnrollment[] $programEnrollments
 */
class Program extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'program';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'company_id'], 'required'],
            [['title', 'description'], 'string'],
            [['company_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'program_id' => 'Program ID',
            'title' => 'Title',
            'company_id' => 'Company',
            'description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModules()
    {
        return $this->hasMany(Module::className(), ['program_id' => 'program_id'])
		->orderBy(['module_order'=>'SORT_ASC']);
    }
	
    /**
     * @return \yii\db\ActiveQuery for published modules
     */	
	public function getPublishedModules()
	{
		return $this->hasMany(Module::className(), ['program_id' => 'program_id'])->orderBy(['module_order'=>'SORT_ASC'])->andOnCondition(['status' => 1]);
	}
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnrollments()
    {
        return $this->hasMany(ProgramEnrollment::className(), ['program_id' => 'program_id']);
    }	
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramEnrollments()
    {
        return $this->hasMany(ProgramEnrollment::className(), ['program_id' => 'program_id'])->joinWith(['userProfile as user_profile'])->orderBy(['user_profile.firstname'=>SORT_ASC]);;
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'company_id']);
    }
	
    /**
     * To reset full program
     */	
	public function resetProgram(){
		foreach($this->modules as $module){
			$units = $module->units;
			foreach($units as $unit){
				$reports = UnitReport::find()->where(['unit_id'=>$unit->unit_id])->all();
				foreach($reports as $report){
					$report->delete();
				}
				//delete awareness answers and cap answers also
				$a_answers = AwarenessAnswer::find()->joinWith(['awareness_question'])->where(['awareness_question.unit_id'=>$unit->unit_id])->all();
				foreach($a_answers as $answer){
					$answer->delete();
				}
				$c_answers = CapabilityAnswer::find()->joinWith(['capability_question'])->where(['capability_question.unit_id'=>$unit->unit_id])->all();
				foreach($c_answers as $answer){
					$answer->delete();
				}
			}
		}
	}
    /**
     * To delete full program
     */		
	public function deleteProgram(){
			$modules = $this->modules;
			foreach($modules as $module){
				$units = Module::findOne($module->module_id)->units;
				foreach($units as $unit){
					foreach($unit->awarenessQuestions as $question){
							AwarenessOption::deleteAll(['question_id'=>$question->aq_id]);
							AwarenessAnswer::deleteAll(['question_id'=>$question->aq_id]);
					}
					foreach($unit->capabilityQuestions as $question){
							CapabilityAnswer::deleteAll(['question_id'=>$question->cq_id]);
					}
					AwarenessQuestion::deleteAll(['unit_id'=>$unit->unit_id]);
					CapabilityQuestion::deleteAll(['unit_id'=>$unit->unit_id]);
					UnitReport::deleteAll(['unit_id'=>$unit->unit_id]);
					UnitElement::deleteAll(['unit_id'=>$unit->unit_id]);
				}
				Unit::deleteAll(['module_id'=>$module->module_id]);
			}
			ProgramEnrollment::deleteAll(['program_id'=>$this->program_id]);
			Module::deleteAll(['program_id'=>$this->program_id]);
			$this->delete();
			return true;
	}
	
	
	
	
	
	public function getAllEnrolledUserProgram($company_id,$program_id){
		
		
		$connection = \Yii::$app->db;
		$location = "";
		 if(!Yii::$app->user->can("company_admin")){	
			 if(Yii::$app->user->can("group_assessor")){		
				$setlocation = \Yii::$app->user->identity->userProfile->access_location;			  
				$location = "and up.location in (".$setlocation.")";
			}
			else if(Yii::$app->user->can("local_assessor")){
				$setlocation = \Yii::$app->user->identity->userProfile->location;				
				$location = " and up.location = ".$setlocation;
			}
		 }	  
		$model = $connection->createCommand("select u.id from user u, user_profile up , program p, program_enrollment pe where u.id= up.user_id and u.c_id = ".$company_id." and p.company_id = ".$company_id." ".$location." and p.program_id = pe.program_id and u.id = pe.user_id and p.program_id = ".$program_id);
		$company_users = $model->queryAll();
		
		$all_users = array();
		foreach ($company_users as $key=>$tmp) {
			$all_users[$key] = $tmp['id'];
		
		}
		$list_user = implode(",",$all_users);
		
		/* echo $list_user;
		echo "<br>";  */
		
		if(empty($list_user))
		{
			/* echo "Empty Statement False Functionality";
			exit; */
			
			return 0;
		}
		
		
		$model2 = $connection->createCommand("SELECT ut.unit_id FROM program p, module m, unit ut WHERE p.program_id = m.program_id and m.module_id = ut.module_id and p.program_id = ".$program_id);
		$unit_details = $model2->queryAll();
		
		//print_r($unit_details);
		
		$unit_total_per = 0;
		$all_total_per = 0;
		$no_user = count($all_users);
		//echo "total_user".$no_user."<br>";
		
		foreach ($unit_details as $key=>$unit) {
			$n_tests = 0;
			$c_status = CapabilityQuestion::find()->where(['unit_id'=>$unit['unit_id']])->one();
			if(!$c_status)
				$n_tests = $n_tests + 50;
			
			$total_per = 0;
			$no_awareness_progress = 0;
			$model3 = $connection->createCommand("SELECT count(*)as unituser FROM `unit_report` WHERE unit_id=".$unit['unit_id']." and awareness_progress = '100' and student_id in (".$list_user.")");
			
			/* $report = $model3->query();
			print_r($report);  */
			
			
			$n_tests = $n_tests + 50;
			$report = $model3->queryOne();
			$no_awareness_progress = $report['unituser'];
			//echo "awareness_progress".$no_awareness_progress."<br>";
			$total_per = ($no_awareness_progress * $n_tests )/$no_user;
			$unit_total_per = $unit_total_per + round($total_per);  
			
							
		}
		
		
		/* echo $total_per ;		
		echo "<br>"; */
		$all_units = count($unit_details); 
		/* echo $all_units ;
		echo "<br>"; */
			
		$all_total_per = $unit_total_per/$all_units;
		/* echo $all_total_per ;
		echo "<br>";
			exit; */
		return round($all_total_per); 
    }	
	
}

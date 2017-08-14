<?php

namespace common\models;

use Yii;
use common\models\CapabilityQuestion;
use common\models\UnitReport;
use common\models\User;

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
	
	
	
	
	
	public function getAllEnrolledUserProgram($company_id,$program_id,$firstname="",$lastname="",$role="",$division="",$location="",$state=""){
		
		$whereCondition ="";
		$setlocation = "";
		if(isset($firstname)&& !empty($firstname))
			$whereCondition .= " and up.firstname like '%".$firstname."%'";
		if(isset($lastname)&& !empty($lastname))
			$whereCondition .= " and up.lastname like '%".$lastname."%'";
		if(isset($role)&& !empty($role))
			$whereCondition .= " and up.role = '".$role."'";
		if(isset($division)&& !empty($division))
			$whereCondition .= " and up.division = '".$division."'";
		if(isset($location)&& !empty($location))
			$whereCondition .= " and up.location = '".$location."'";
		if(isset($state)&& !empty($state))
			$whereCondition .= " and up.state = '".$state."'";
		
		$connection = \Yii::$app->db;
		if(empty($location))
		{
		
		 if((!Yii::$app->user->can("superadmin")) && (!Yii::$app->user->can("company_admin"))){	
			 if(Yii::$app->user->can("group_assessor")){		
				//$setlocation = \Yii::$app->user->identity->userProfile->access_location;	
				$users_details = User::findOne(\Yii::$app->user->id);
				$setlocation = $users_details->userProfile->access_location;
				$setlocation = "and up.location in (".$setlocation.")";
			}
			else if(Yii::$app->user->can("local_assessor")){
				$setlocation = \Yii::$app->user->identity->userProfile->location;				
				$setlocation = " and up.location = ".$setlocation;
			}
		 }
		}
		
		$model = $connection->createCommand("select u.id from user u, user_profile up , program p, program_enrollment pe where u.id= up.user_id and u.c_id = ".$company_id." and p.company_id = ".$company_id." ".$setlocation.$whereCondition." and p.program_id = pe.program_id and u.id = pe.user_id and p.program_id = ".$program_id);
		$company_users = $model->queryAll();
		
		
	 /*   $company_users = $model->query();
		echo "<pre>";
		print_r($company_users);
		exit;   */
		
		$all_users = array();
		foreach ($company_users as $key=>$tmp) {
			$all_users[$key] = $tmp['id'];
		
		}
		$list_user = implode(",",$all_users);
		
		/* echo $list_user;
		echo "<br>"; */ 
		//exit;
		
		if(empty($list_user))
		{
			/* echo "Empty Statement False Functionality";
			exit; */
			
			return false;
		}
		
		
		$model2 = $connection->createCommand("SELECT ut.unit_id FROM program p, module m, unit ut WHERE p.program_id = m.program_id and m.module_id = ut.module_id and p.program_id = ".$program_id);
		$unit_details = $model2->queryAll();
		
		 $setunit_details = $model2->query();
		$all_units = array();
		foreach ($setunit_details as $key=>$tmp) {
			$all_units[$key] = $tmp['unit_id'];		
			}
		$list_units = implode(",",$all_units); 
		
		 
		/* print_r($list_units);
		exit;  */
		
		$unit_total_per = 0;
		$all_total_per = 0;
		$total_all_units = count($unit_details); 
		
		
		$no_user = count($all_users);
	
		//echo "total_user".$no_user."<br>";
		//echo 
		//exit;
		
		foreach ($unit_details as $key=>$unit) {
			//$n_tests = 0;
			$total_aws_per = 0;
			$total_cap_per = 0;
			$no_awareness_progress = 0;
			$no_capability_progress = 0;
			
			$c_status = CapabilityQuestion::find()->where(['unit_id'=>$unit['unit_id']])->one();
			
			if($c_status)
			{	
				//$n_tests = $n_tests + 50;
				
				$model4 = $connection->createCommand("SELECT count(*)as unitcapabuser FROM `unit_report` WHERE unit_id=".$unit['unit_id']." and capability_progress = '100' and student_id in (".$list_user.")");
			
				$report = $model4->queryOne();
				
				$no_capability_progress = $report['unitcapabuser'];
				
				$total_all_units = $total_all_units + 1;
			}
			
			
			$model3 = $connection->createCommand("SELECT count(*)as unitawareuser FROM `unit_report` WHERE unit_id=".$unit['unit_id']." and awareness_progress = '100' and student_id in (".$list_user.")");
			
			/* $report = $model3->query();
			print_r($report);  */
			
			
			//$n_tests = $n_tests + 50;
			$report = $model3->queryOne();
			
			$no_awareness_progress = $report['unitawareuser'];
			
			
			if($no_awareness_progress == 0)
			{
				//$total_all_units = $total_all_units - 1;
			}
			
			
			//echo "awareness_progress".$no_awareness_progress."<br>";
			$total_aws_per = ($no_awareness_progress * 100 );		
				
			$total_cap_per = ($no_capability_progress  * 100 );
			
			
			
			
			$unit_total_per = $unit_total_per + round($total_aws_per) + round($total_cap_per);  
			
			//echo "cap & awsare-> ".$n_tests." unit id ".$unit['unit_id']." report ".$no_awareness_progress." no of Users ".$no_user." all unit points ".$unit_total_per."<br>";				
		}
		
	
		
		
		/* echo $total_per ;		
		echo "<br>"; */
		 
		 /* echo $total_all_units ;
		echo "<br>"; 
			exit; */
			
		if($total_all_units == 0)	
			$all_total_per = 0;
		else 
			$all_total_per = $unit_total_per/($total_all_units * $no_user );
		
		/* echo $all_total_per ;
		echo "<br>";
			exit; */
		
	//	echo " Total no units".$total_all_units." total unit per ".$unit_total_per." program perc ".$all_total_per;
		
		
		
		return round($all_total_per); 
		
    }	
	
}

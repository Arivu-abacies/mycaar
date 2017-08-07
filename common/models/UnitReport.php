<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "unit_report".
 *
 * @property integer $report_id
 * @property integer $unit_id
 * @property integer $student_id
 * @property integer $awareness_progress
 * @property integer $capability_progress
 * @property string $updated_at
 *
 * @property User $student
 */
class UnitReport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unit_report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit_id', 'student_id'], 'required'],
            [['unit_id', 'student_id', 'awareness_progress', 'capability_progress'], 'integer'],
            [['updated_at'], 'safe'],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'report_id' => 'Report ID',
            'unit_id' => 'Unit ID',
            'student_id' => 'Student ID',
            'awareness_progress' => 'Awareness Progress',
            'capability_progress' => 'Capability Progress',
            'updated_at' => 'Date & Time',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['unit_id' => 'unit_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'student_id']);
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser_profile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'student_id']);
    }
	
	 public function getAuthRole()
    {
        return $this->hasOne(AuthAssignment::className(), ['user_id' => 'student_id']);
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssessor()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'cap_done_by']);
			
    }
				
	public function resetUser(){
		
		//delete awareness answers and cap answers also
		$a_answers = AwarenessAnswer::find()->joinWith(['awareness_question'])->where(['awareness_question.unit_id'=>$this->unit_id,'user_id'=>$this->student_id])->all();
		foreach($a_answers as $answer){
			$answer->delete();
		}
		$c_answers = CapabilityAnswer::find()->joinWith(['capability_question'])->where(['capability_question.unit_id'=>$this->unit_id,'user_id'=>$this->student_id])->all();
		foreach($c_answers as $answer){
			$answer->delete();
		}	
		$this->capability_progress = NULL;
		$this->cap_done_by = NULL;
		$this->awareness_progress = NULL;
	}
	public function resetAwTest(){
		$a_answers = AwarenessAnswer::find()->joinWith(['awareness_question'])->where(['awareness_question.unit_id'=>$this->unit_id,'user_id'=>$this->student_id])->all();
		foreach($a_answers as $answer){
			$answer->delete();
		}		
	}
	public function resetCpTest(){
		$c_answers = CapabilityAnswer::find()->joinWith(['capability_question'])->where(['capability_question.unit_id'=>$this->unit_id,'user_id'=>$this->student_id])->all();
		foreach($c_answers as $answer){
			$answer->delete();
		}			
	}
}

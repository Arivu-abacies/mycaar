<?php

namespace common\models;

use Yii;

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
}

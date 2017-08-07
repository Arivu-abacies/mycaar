<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "module".
 *
 * @property integer $module_id
 * @property integer $program_id
 * @property string $title
 * @property string $short_description
 * @property string $featured_video_url
 * @property string $detailed_description
 * @property integer $status
 *
 * @property Program $program
 * @property ModuleInstructor[] $moduleInstructors
 * @property Unit[] $units
 */
class Module extends \yii\db\ActiveRecord
{
	public $featured_video_upload;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'module';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_id', 'title'], 'required'],
            [['program_id', 'status','module_order'], 'integer'],
            //[['short_description', 'detailed_description'], 'string'],
			[['short_description', 'detailed_description','featured_video_url'], 'string'],
			[['featured_image'], 'file','extensions' => 'jpg,png', 'skipOnEmpty' => true],
			[['featured_video_upload'], 'file','extensions' => 'mp4,m4v,webm,ogv', 'skipOnEmpty' => true],
            [['title'], 'string', 'max' => 1000],
			[['language'], 'string', 'max' => 200],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['program_id' => 'program_id']],
			[['course_start_date','course_end_date','enrl_start_date','enrl_end_date','is_course_open_anytime','is_enrlmnt_open_anytime'],'safe'],
			
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'module_id' => 'Module ID',
            'program_id' => 'Program ID',
            'title' => 'Module Name',
            'short_description' => 'Short Description',
            'featured_video_url' => 'Feattured Video',
            'featured_video_upload' => 'Feattured Video Upload',
            'detailed_description' => 'Detailed Description',
            'status' => 'Status',
			'language' => 'Module Language',
			'module_order' => 'Module Order',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['program_id' => 'program_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModuleInstructors()
    {
        return $this->hasMany(ModuleInstructor::className(), ['module_id' => 'module_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnits()
    {
        return $this->hasMany(Unit::className(), ['module_id' => 'module_id'])->orderBy(['unit_order' => SORT_ASC]);;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPublishedUnits()
    {
        return $this->hasMany(Unit::className(), ['module_id' => 'module_id'])->orderBy(['unit_order' => SORT_ASC])->andOnCondition(['status' => 1]);
    }	
	
	public function uploadImage(){
//		if($this->validate()) {
			$this->featured_image->saveAs('uploads/' . $this->featured_image->baseName . '.' .$this->featured_image->extension);
			$this->featured_image = 'uploads/'.$this->featured_image->baseName.'.'.$this->featured_image->extension;
			return true;
//		}else
//			return false;
	}
	
	
	public function uploadVideo(){
		//if($this->validate()) {
			/* $this->featured_video_url->saveAs('uploads/' . $this->featured_video_url->baseName . '.' .$this->featured_video_url->extension);
			$this->featured_video_url = 'uploads/'.$this->featured_video_url->baseName.'.'.$this->featured_video_url->extension;
			return true; */
		/* }else
			return false; */
	}
	
	public function resetModule(){
		foreach($this->units as $unit){
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

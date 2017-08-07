<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "unit".
 *
 * @property integer $unit_id
 * @property integer $module_id
 * @property string $title
 * @property integer $status
 *
 * @property AwarenessQuestion[] $awarenessQuestions
 * @property CapabilityQuestion[] $capabilityQuestions
 * @property Module $module
 * @property UnitElement[] $unitElements
 * @property UnitReport[] $unitReports
 */
class Unit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_id', 'title'], 'required'],
            [['module_id', 'status', 'unit_order'], 'integer'],
			[['auto_reset_period'], 'integer'],
            [['title'], 'string', 'max' => 1000],
            [['module_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::className(), 'targetAttribute' => ['module_id' => 'module_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'unit_id' => 'Unit ID',
            'module_id' => 'Module ID',
            'title' => 'Title',
            'status' => 'Status',
			'unit_order' => 'Unit Order',
			'auto_reset_period' => 'AutoReset Period'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAwarenessQuestions()
    {
        return $this->hasMany(AwarenessQuestion::className(), ['unit_id' => 'unit_id'])->orderBy(['order_id'=>'SORT_ASC']);;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCapabilityQuestions()
    {
        return $this->hasMany(CapabilityQuestion::className(), ['unit_id' => 'unit_id'])->orderBy(['order_id'=>'SORT_ASC']);;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(Module::className(), ['module_id' => 'module_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnitElements()
    {
        return $this->hasMany(UnitElement::className(), ['unit_id' => 'unit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnitReports()
    {
        return $this->hasMany(UnitReport::className(), ['unit_id' => 'unit_id']);
    }
	
	public function resetUnit(){
			$reports = UnitReport::find()->where(['unit_id'=>$this->unit_id])->all();
			foreach($reports as $report){
				$report->delete();
			}
			//delete awareness answers and cap answers also
			$a_answers = AwarenessAnswer::find()->joinWith(['awareness_question'])->where(['awareness_question.unit_id'=>$this->unit_id])->all();
			foreach($a_answers as $answer){
				$answer->delete();
			}
			$c_answers = CapabilityAnswer::find()->joinWith(['capability_question'])->where(['capability_question.unit_id'=>$this->unit_id])->all();
			foreach($c_answers as $answer){
				$answer->delete();
			}		
	}
}

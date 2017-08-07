<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "reset_schedule".
 *
 * @property integer $s_id
 * @property integer $unit_id
 * @property string $cron_time
 * @property string $updated_at
 */
class ResetSchedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reset_schedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit_id', 'cron_time'], 'required'],
            [['unit_id'], 'integer'],
            [['updated_at','actual_time'], 'safe'],
            [['cron_time'], 'string', 'max' => 50],
			//[['actual_time'], 'string', 'max' => 1200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            's_id' => 'S ID',
            'unit_id' => 'Unit ID',
            'cron_time' => 'Cron Time',
            'updated_at' => 'Updated At',
			'actual_time' => 'Scheduled At'
        ];
    }
	
	public function getUnit(){
		return $this->hasOne(Unit::className(), ['unit_id'=>'unit_id']);
	}
}

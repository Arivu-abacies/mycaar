<?php

namespace common\models;
use Yii;

/**
 * This is the model class for table "quick_email".
 *
 * @property integer $q_id
 * @property integer $c_id
 * @property string $to_email
 * @property string $from_email
 * @property string $subject
 * @property string $message
 * @property integer $status
 * @property string $datetime
 */
class QuickEmail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'quick_email';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['c_id', 'user_id', 'to_email', 'from_email', 'subject', 'message'], 'required'],
            [['c_id', 'user_id','status'], 'integer'],
            [['message'], 'string'],
            [['to_email', 'from_email', 'subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'q_id' => 'Q ID',
            'c_id' => 'C ID',
            'user_id' => 'User ID',
            'to_email' => 'To Email',
            'from_email' => 'From Email',
            'subject' => 'Subject',
            'message' => 'Message',
            'status' => 'Status',
            'datetime' => 'Datetime',
        ];
    }
	
 	
}

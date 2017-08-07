<?php

namespace common\models;

use Yii;


/**
 * This is the model class for table "enrolment".
 *
 * @property integer $program_id
 * @property integer $id
 * @property string $username
 * @property integer $c_id
 * @property string $enrolled
 */
class Enrolment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enrolment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_id', 'id', 'c_id', 'is_enrolled'], 'integer'],
            [['program_id'], 'required'],
            [['username'], 'string', 'max' => 255], 
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
         return [
            'program_id' => 'Program ID',
            'id' => 'ID',
            'username' => 'Username',
            'c_id' => 'C ID',
            'is_enrolled' => 'is_enrolled',
        ]; 
    }
	 

}

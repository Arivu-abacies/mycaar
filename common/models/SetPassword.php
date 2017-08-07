<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $password_reset_token
 * @property string $email

 */
class SetPassword extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			['email', 'email'],
            [['password_hash','id'], 'required'], 
			[['password_hash','id'], 'trim'], 
			[['password_hash'], 'string', 'max' => 15, 'min'=>6],	
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',       
        ];
    }
	
	
	
}

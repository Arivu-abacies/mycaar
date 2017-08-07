<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "state".
 *
 * @property integer $state_id
 * @property integer $company_id
 * @property string $name
 *
 * @property UserProfile[] $userProfiles
 */
class State extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'state';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'name'], 'required'],
            [['company_id'], 'integer'],
            [['name'], 'string', 'max' => 200],
			
			[['name'],'validateCompanyWiseStateName'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'state_id' => 'State ID',
            'company_id' => 'Company Name',
            'name' => 'State Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfiles()
    {
        return $this->hasMany(UserProfile::className(), ['state' => 'state_id']);
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'company_id']);
    }
	
	// Validation For Company Wise with Database By Arivazhagan
	
	public function validateCompanyWiseStateName()
    { 		
		if($this->state_id)
		{
		  $checkCompanyWise = static::find()
		  ->andWhere(['name'=>$this->name])
		  ->andWhere(['company_id'=>$this->company_id])
		  ->andWhere(['<>','state_id',$this->state_id])->one();
		}else 
		{	
			$checkCompanyWise = static::findOne(['name'=>$this->name,'company_id'=>$this->company_id]);
		}
		
       if($checkCompanyWise)
	   {
		 $this->addError('name','Already Added This State Name');
	   } 
    }
}

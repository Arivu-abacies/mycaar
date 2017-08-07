<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "division".
 *
 * @property integer $division_id
 * @property integer $company_id
 * @property string $title
 * @property string $description
 *
 * @property UserProfile[] $userProfiles
 */
class Division extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'division';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'title'], 'required'],
            [['company_id'], 'integer'],
            [['title'], 'string', 'max' => 200],
            [['description'], 'string', 'max' => 1000],
			
			[['title'],'validateCompanyWiseDivisionName'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'division_id' => 'Division ID',
            'company_id' => 'Company Name',
            'title' => 'Division Name',
            'description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfiles()
    {
        return $this->hasMany(UserProfile::className(), ['division' => 'division_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'company_id']);
    }
	
	// Validation For Company Wise with Database By Arivazhagan
	
	public function validateCompanyWiseDivisionName()
    { 		
		if($this->division_id)
		{
		  $checkCompanyWise = static::find()
		  ->andWhere(['title'=>$this->title])
		  ->andWhere(['company_id'=>$this->company_id])
		  ->andWhere(['<>','division_id',$this->division_id])->one();
		}else 
		{
			$checkCompanyWise = static::findOne(['title'=>$this->title,'company_id'=>$this->company_id]);
		}
       if($checkCompanyWise)
	   {
		 $this->addError('title','Already Added This Division Name');
	   } 
    }
	
	
}

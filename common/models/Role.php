<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "role".
 *
 * @property integer $role_id
 * @property integer $company_id
 * @property string $title
 * @property string $description
 *
 * @property Company $company
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'title'], 'required'],
            [['company_id'], 'integer'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 1000],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'company_id']],
			
			[['title'],'validateCompanyWiseRoleName'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id' => 'Role ID',
            'company_id' => 'Company ID',
            'title' => 'Role Name',
            'description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'company_id']);
    }
	
	// Validation For Company Wise with Database By Arivazhagan
	public function validateCompanyWiseRoleName()
    { 			
		if($this->role_id)
		{
		  $checkCompanyWise = static::find()
		  ->andWhere(['title'=>$this->title])
		  ->andWhere(['company_id'=>$this->company_id])
		  ->andWhere(['<>','role_id',$this->role_id])->one();
		}else 
		{
		  $checkCompanyWise = static::findOne(['title'=>$this->title,'company_id'=>$this->company_id]);
		}
       if($checkCompanyWise)
	   {
		 $this->addError('title','Already Added This Role Name');
	   } 
    }
}

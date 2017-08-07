<?php

namespace common\models;

use Yii;



/**
 * This is the model class for table "company".
 *
 * @property integer $company_id
 * @property string $name
 * @property string $about_us
 * @property string $logo
 * @property integer $admin
 *
 * @property User $admin0
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
	 	  
    public static function tableName()
    {
        return 'company';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'admin','slug'], 'required'],          
            [['about_us', 'logo'], 'required','on' => 'update_by_company_admin'],          
            [['about_us'], 'string'],
			[['logo'], 'file','extensions' => 'jpg,jpeg,png', 'skipOnEmpty' => true],
            [['admin'], 'integer'],
            [['name'], 'string', 'max' => 200],
            [['admin'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['admin' => 'id']],
			[['slug'], 'unique','targetClass' => '\common\models\Company', 'message' => 'This Slug Url has already been taken.'],
        ];
    }

	public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update_by_company_admin'] = ['name','about_us','logo','slug','admin']; //Scenario Values Only Accepted
        return $scenarios;
    } 
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'company_id' => 'Company ID',
            'name' => 'Name',
            'about_us' => 'About Us',
            'logo' => 'Logo',
            'admin' => 'Admin',
            'upfile' => 'Upfile',
            'slug' => 'Slug Url',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyAdmin()
    {
        return $this->hasOne(User::className(), ['id' => 'admin']);
    }
	
	public function getPrograms(){
		
		return $this->hasMany(Program::className(), ['company_id' => 'company_id']);
		
	}
	/**
     * Upload the Company Logo On Particular Folder Structure
     */
	 
	public function uploadImage(){	
//		if($this->validate()) {	
			$this->logo->saveAs('uploads/company_logo/'.$this->logo->baseName.'.'.$this->logo->extension);
			$this->logo = 'uploads/company_logo/'.$this->logo->baseName.'.'.$this->logo->extension;
			return true;	
//		 }else {
//			return false;
//		}	 	
	}	
	public function deleteCompany(){
		Division::deleteAll(['company_id'=>$this->company_id]);
		Location::deleteAll(['company_id'=>$this->company_id]);
		State::deleteAll(['company_id'=>$this->company_id]);
		Role::deleteAll(['company_id'=>$this->company_id]);
		$programs = $this->programs;
		foreach($programs as $program){
			$program->deleteProgram();
		}
		$this->delete();		
	}
}

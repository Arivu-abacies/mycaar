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
class ImportFile extends \yii\db\ActiveRecord
{
	
  public $upfile;
  public $company_id;
		

  	/**
     * Return All Users Details Depends Upon Role Name From DB
     */
  public function rules()
    {
        return [
            [['company_id','upfile'], 'required'],                  			
			[['upfile'], 'file','extensions' => 'xlsx,xls'],           
        ];
    }
	
	
   public function attributeLabels()
    {
        return [
            'company_id' => 'Company Name',          
            'upfile' => 'Upload File',          
        ];
    }
	
}

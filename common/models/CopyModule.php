<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\models\Module;
/**
 * Login form
 */
class CopyModule extends Model
{
   public $program_id;
   public $copy_program;
   public $copy_module;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_id', 'copy_program','copy_module'], 'required'],			
        ];
    }

	  public function attributeLabels()
    {
        return [
            'program_id' => 'Program Name',
            'copy_program' => 'Copy To Program Name',
            'copy_module' => 'Copy Of Module',
         
        ];
    }
	
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "site_meta".
 *
 * @property integer $meta_id
 * @property integer $user_id
 * @property string $meta_key
 * @property string $meta_value
 */
class SiteMeta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
	 
	public $rightsidelogo;
	public $leftsidelogo;
	
    public static function tableName()
    {
        return 'site_meta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [  
            [['meta_key','meta_value'], 'string'],
            [['rightsidelogo','leftsidelogo'], 'safe'],
            [['rightsidelogo'], 'file','extensions' => 'jpg,png', 'skipOnEmpty' => true], 
            [['leftsidelogo'], 'file','extensions' => 'jpg,png', 'skipOnEmpty' => true], 
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'meta_id' => 'Meta ID',
            'meta_value' => 'Meta Value',
            'meta_key' => 'Meta Key',
        ];
    }

	public function uploadrightImage(){	
		$this->rightsidelogo->saveAs('img/'.$this->rightsidelogo->baseName.'.'.$this->rightsidelogo->extension);
		$this->rightsidelogo = 'img/'.$this->rightsidelogo->baseName.'.'.$this->rightsidelogo->extension;
		return true;			 	
	}	
	
	public function uploadleftImage(){				
			$this->leftsidelogo->saveAs('img/'.$this->leftsidelogo->baseName.'.'.$this->leftsidelogo->extension);
			$this->leftsidelogo = 'img/'.$this->leftsidelogo->baseName.'.'.$this->leftsidelogo->extension;
		 	return true;			 	
	}
}

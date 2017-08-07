<?php

namespace common\models;

use Yii;
use common\models\User;
/**
 * This is the model class for table "program_enrollment".
 *
 * @property integer $e_id
 * @property integer $program_id
 * @property integer $user_id
 *
 * @property Program $program
 * @property User $user
 */
class ProgramEnrollment extends \yii\db\ActiveRecord
{
    public $selected_program;
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'program_enrollment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_id', 'user_id'], 'required'],
            [['program_id', 'user_id'], 'integer'],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['program_id' => 'program_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'e_id' => 'E ID',
            'program_id' => 'Program ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['program_id' => 'program_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */	
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'user_id']);
    }	
	
	public function isEnrolled(){		
		 return false;		 
	 }
	 
	
public function sendEnrollmentEmail($id,$programname)
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'id' => $id,
        ]);
			
        if (!$user) {
            return false;
        }
		
        $message = Yii::$app
            ->mail
            ->compose(
                ['html' => 'enrolmentSuccessMessage-html'],
                ['user' => $user,'programname'=>$programname]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => 'MyCaar '])
            ->setTo($user->email)
            ->setSubject('MyCaar Enrollment Confirmation');
       	
		$message->getSwiftMessage()->getHeaders()->addTextHeader('MIME-version', '1.0\n');
		$message->getSwiftMessage()->getHeaders()->addTextHeader('charset', ' iso-8859-1\n');
		
		return $message->send(); 
    }	
	
	
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_profile".
 *
 * @property integer $profile_id
 * @property integer $user_id
 * @property string $firstname
 * @property string $lastname
 * @property integer $position
 * @property integer $location
 * @property integer $state
 *
 * @property User $user
 * @property Division $position0
 * @property Location $location0
 * @property State $state0
 */
class UserProfile extends \yii\db\ActiveRecord
{
	//public $fullname;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required','except' => ['company_admin_user'] ],
			[['firstname', 'lastname'], 'required','on' => ['company_admin_user']],
		
            [['user_id', 'division', 'location', 'state','role'], 'integer'],
            [['firstname', 'lastname','employee_number'], 'string', 'max' => 100],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['division'], 'exist', 'skipOnError' => true, 'targetClass' => Division::className(), 'targetAttribute' => ['division' => 'division_id']],
            [['location'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location' => 'location_id']],
            [['state'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['state' => 'state_id']],
			['access_location','safe'],
        ];
    }

	
	
	public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['company_admin_user'] = ['firstname','lastname','division','location','state','role','employee_number',
		'access_location'];//Scenario Values Only Accepted
        return $scenarios;
    }
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'profile_id' => 'Profile ID',
            'user_id' => 'User ID',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'division' => 'Division',
            'location' => 'Location',
            'state' => 'State',
            'role' => 'Role',
            'employee_number' => 'Employee Number',
            'access_location' => 'Access Location',
        ];
    }
	
	public function getFullname(){
		 return $this->firstname. " ". $this->lastname;
	}
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
       return $this->hasOne(User::className(), ['id' => 'user_id']);
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
    public function getDivisionModel()
    {
       // return $this->hasOne(Division::className(), ['division_id' => 'position']);
        return $this->hasOne(Division::className(), ['division_id' => 'division']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationModel()
    {
        return $this->hasOne(Location::className(), ['location_id' => 'location']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStateModel()
    {
        return $this->hasOne(State::className(), ['state_id' => 'state']);
    }
	
	 /**
     * @return \yii\db\ActiveQuery created function by Arivazhagan
     */
     public function getRoleModel()
    {
        return $this->hasOne(Role::className(), ['role_id' => 'role']);
    } 
}

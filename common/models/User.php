<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
 
class User extends ActiveRecord implements IdentityInterface
{
	public $fullname;
	public $role;
	public $password;
	public $old_password;
	public $new_password;
	public $confirm_password;

	
    const STATUS_DELETED = 12;
    const STATUS_ACTIVE = 10;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'role'], 'required','except' => ['apply_changepassword','apply_setpassword']],
			['email', 'email'],
			['password','string','min'=>6,'max'=>15],
			//[['password'], 'required', 'except' => ['update_by_admin','update_by_company_admin','apply_forgotpassword']],
            [['c_id'], 'integer'],           
            [['username', 'email'], 'string', 'max' => 255],
            [['username'], 'unique','targetClass' => '\common\models\User', 'message' => 'This Username has already been taken.'],
            [['email'], 'unique','targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],
            [['password_reset_token'], 'unique'],			
			['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
			
			
			[['old_password','new_password','confirm_password'],'required','on' => ['apply_changepassword']],
			[['old_password'],'validateCurrentPassword'],
			[['new_password','confirm_password'],'string','min'=>6,'max'=>15],
			[['new_password','confirm_password'],'filter','filter'=>'trim'],
			[['confirm_password'],'compare','compareAttribute'=>'new_password','message'=>'Password do not match'],
        ];
    }
	
	public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update_by_admin'] = ['role','c_id','email'];//Scenario Values Only Accepted
        $scenarios['update_by_company_admin'] = ['role','c_id','email','password'];//Scenario Values Only Accepted
        $scenarios['apply_forgotpassword'] = ['email'];//Scenario Values Only Accepted
        $scenarios['apply_setpassword'] = ['password_hash','password_reset_token'];//Scenario Values Only Accepted
        $scenarios['apply_changepassword'] = ['old_password','new_password','confirm_password'];//Scenario Values Only Accepted
        return $scenarios;
    }
	  /**
     * @inheritdoc
     */
	public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password' => 'Password',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'role' => 'Role',
            'c_id' => 'Company',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
	
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {		
		$user = static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);		
        $profile = UserProfile::find()->where(['user_id'=>$id])->one();	 			
		if($user && $profile)
		{
			$roles = Yii::$app->authManager->getRolesByUser($id);
			reset($roles);
			/* @var $role \yii\rbac\Role */
			$role = current($roles);
			$user->role = $role->name;
			
			$user->fullname= $profile->firstname." ".$profile->lastname;				
		/* 	switch ($user->role) {
				case 3:
					$role = "Superadmin" ;
					break;
				case 2:
					$role = "Companyadmin" ;
					break;
				case 1:
					$role = "Assessor" ;
					break;
				default:
					$role = "User";
			}	
			$user->role = $role; */
		}
		return $user;		
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
       // return static::findOne(['username' => $username, 'user_status' => self::STATUS_ACTIVE]);
     
        $val = static::find()->where("status = ".self::STATUS_ACTIVE." AND (username = '$username' OR email = '$username')")->one();		
		 return $val;
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
	
	/**
	 * Returns user role name according to RBAC
	 * @return string
	 */
	public function getRoleName()
	{
		$roles = Yii::$app->authManager->getRolesByUser($this->id);
		if (!$roles) {
			return null;
		}
		reset($roles);
		/* @var $role \yii\rbac\Role */
		$role = current($roles);

		return $role->name;
	}
	

	 /**
     * Validates Current password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validateCurrentPassword()
    { 
       if(!$this->verifyPassword($this->old_password))
	   {
		   $this->addError('old_password','Current Password Incorrect');
	   }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }

	public function getUserRole()
	{
		return $this->hasOne(Role::className(), ['role_id' => 'role'])->via('userProfile');
	}

	 /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthRole()
    {
        return $this->hasOne(AuthAssignment::className(), ['user_id' => 'id']);
    }
	
	 /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'c_id']);
    }
	
	 public function verifyPassword($password)
    {
       $dbpassword = static::findOne(['username'=>Yii::$app->user->identity->username,'status'=>self::STATUS_ACTIVE])->password_hash;
	   
	   return Yii::$app->security->validatePassword($password, $dbpassword);
    }
	
	 public function getPrograms(){
		 return ProgramEnrollment::find()->where(['user_id'=>$this->id])->all();
	 }
	 
	 public function getUnitProgress($unit_id){
		 
		 $report = UnitReport::find()->where(['unit_id'=>$unit_id,'student_id'=>$this->id])->one();
		 $output = ['ap'=>'red','cp'=>'red'];
		 $c_status = CapabilityQuestion::find()->where(['unit_id'=>$unit_id])->one();
			 if(!$c_status)
				 $output['cp'] = 'grey';
		 if(!$report)
			 return $output;
		 else 
		 {
				 $output = ['ap'=>'amber','cp'=>'amber'];
				 if(!$c_status) //if no capability tests for the unit
					$output['cp'] = 'grey';
				 else if(is_null($report->capability_progress))
					 $output['cp'] = 'red';
				 if(is_null($report->awareness_progress))
					 $output['ap'] = 'red';
			// }
			 //then see the progress
			 if($report->awareness_progress == 100)
				 $output['ap'] = 'green';
			 if($report->capability_progress == 100)
				 $output['cp'] = 'green';			 
		 }
			 return $output; 
	 }	 
	 //deprecated, cause the client discarded this logic
	 public function getProgramProgressDeprecated($program_id){
		 $program = Program::findOne($program_id);
		 //total units
		 $modules = $program->modules;
		 $total_modules = count($modules);
		 $modules_completed = [];
		 foreach($modules as $module){
		 if($module->status){ //if module published
			$units = $module->units; 
			$total_units = count($units);
			$units_completed = [];
			foreach($units as $unit)
			{
				if($unit->status){ //if unit published
					$capabilty_progress = $awareness_progress = 0;
					$c_status = CapabilityQuestion::find()->where(['unit_id'=>$unit->unit_id])->one();
					$report = UnitReport::find()->where(['unit_id'=>$unit->unit_id,'student_id'=>$this->id])->one();
					if($report){
						if(!$c_status)
							$capabilty_progress = 100;
						else $capabilty_progress = $report->capability_progress;
						$awareness_progress = $report->awareness_progress;					
					}				
					if($capabilty_progress == 100 && $awareness_progress == 100){
						
						$units_completed[] = $unit->unit_id;
					}					
				}// if unit published
			}
			//print_R($units_completed);
			if(count($units_completed) == $total_units)
				$modules_completed[] = $module->module_id;
			}//module published
		 }

		 //print_R($modules_completed);
		 //total units completed (aw + cp)
		 $progress =  (count($modules_completed)/$total_modules)*100;
		 return (int)$progress;
	 }
	 
	 public function getProgramProgress($program_id){
		 $program = Program::findOne($program_id);
		 if($program){
		 //total units
		 $modules = $program->publishedModules;
		 $total_tests = 0;
		 $tests_completed = 0;
		 foreach($modules as $module){
//			if($module->status){ //if module published 
				$units = $module->publishedUnits; 
 				foreach($units as $unit){
//					if($unit->status){ //if unit published
						$n_tests = 2;
						$capabilty_progress = $awareness_progress = 0;
						$c_status = CapabilityQuestion::find()->where(['unit_id'=>$unit->unit_id])->one();
						if(!$c_status)
							$n_tests = 1;
						$report = UnitReport::find()->where(['unit_id'=>$unit->unit_id,'student_id'=>$this->id])->one();
						if($report){
							$capabilty_progress = $report->capability_progress;
							$awareness_progress = $report->awareness_progress;					
						}				
						if($capabilty_progress == 100 )					
							$tests_completed = $tests_completed+1;
						if($awareness_progress == 100 )					
							$tests_completed = $tests_completed+1;

						$total_tests = 	$total_tests + $n_tests;
//					}//unit status
				}
//			}//module status
		 }

		 //print_R($modules_completed);
		 //total units completed (aw + cp)
		 if($total_tests == 0)
			 return 0;
		 $progress =  ($tests_completed/$total_tests)*100;
		 //if($progress > 100)
		//	 return 100;
		 //else
			 return (int)$progress;			 
		 }
		else return false;
	 }
	 
 public function sendEmail($password)
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
			
        if (!$user) {
            return false;
        }
        
        $message = Yii::$app
            ->mail
            ->compose(
                ['html' => 'passwordSend-text'],
                ['user' => $user,'password'=>$password]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => 'MyCaar '])
            ->setTo($this->email)
            ->setSubject('MyCaar Please Verified your Email');
       
		$message->getSwiftMessage()->getHeaders()->addTextHeader('MIME-version', '1.0\n');
		$message->getSwiftMessage()->getHeaders()->addTextHeader('charset', ' iso-8859-1\n');
		
		return $message->send();
    }


	public function sendEmailForgotPassword()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
			
        if (!$user) {
            return false;
        }
        
        $message = Yii::$app
            ->mail
            ->compose(
                ['html' => 'passwordResetToken-html'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => 'MyCaar '])
            ->setTo($this->email)
            ->setSubject('MyCaar Please Reset Your Password');
       	
		$message->getSwiftMessage()->getHeaders()->addTextHeader('MIME-version', '1.0\n');
		$message->getSwiftMessage()->getHeaders()->addTextHeader('charset', ' iso-8859-1\n');
		
		return $message->send();
    }	
	
	
	public function sendEmailChangePassword()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
			
        if (!$user) {
            return false;
        }
        
        $message = Yii::$app
            ->mail
            ->compose(
                ['html' => 'changepasswordSuccessMessage-html'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => 'MyCaar '])
            ->setTo($this->email)
            ->setSubject('MyCaar Change Password Success');
       	
		$message->getSwiftMessage()->getHeaders()->addTextHeader('MIME-version', '1.0\n');
		$message->getSwiftMessage()->getHeaders()->addTextHeader('charset', ' iso-8859-1\n');
		
		return $message->send();
    }	
	
	
	 public function isEnrolled($program_id){
		 
		$enrolled = ProgramEnrollment::find()->where(['user_id'=>$this->id,'program_id'=>$program_id])->one();
		 //see if the user is enrolled
		 if($enrolled != null)
			 return true;
		 else return false;
		 
	 }
	 
	 public function deleteUser(){		 
		 $this->status = User::STATUS_DELETED;
		 $this->save(false);		 
	 }	 
	 
	public function sendRollChangeEmail($id)
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
                ['html' => 'roleChanged-Send-html'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => 'MyCaar '])
			->setTo($user->email)
            ->setSubject('MyCaar Access Level Confirmation');
       	
		$message->getSwiftMessage()->getHeaders()->addTextHeader('MIME-version', '1.0\n');
		$message->getSwiftMessage()->getHeaders()->addTextHeader('charset', ' iso-8859-1\n');
		
		$message->send(); 
    }	
	
	 
	 public function getProgramoverallProgress($user_id,$program_id){
		$enrolled = ProgramEnrollment::find()->where(['user_id'=>$user_id,'program_id'=>$program_id])->one();
		if($enrolled)
		{
		 $program = Program::findOne($program_id);
		 $modules = $program->publishedModules;
		 $total_tests = 0;
		 $tests_completed = 0;
		 foreach($modules as $module){
				$units = $module->publishedUnits; 
 				foreach($units as $unit){
						$n_tests = 2;
						$capabilty_progress = $awareness_progress = 0;
						$c_status = CapabilityQuestion::find()->where(['unit_id'=>$unit->unit_id])->one();
						if(!$c_status)
							$n_tests = 1;
						$report = UnitReport::find()->where(['unit_id'=>$unit->unit_id,'student_id'=>$user_id])->one();
						if($report){
							$capabilty_progress = $report->capability_progress;
							$awareness_progress = $report->awareness_progress;					
						}				
						if($capabilty_progress == 100 )					
							$tests_completed = $tests_completed+1;
						if($awareness_progress == 100 )					
							$tests_completed = $tests_completed+1;

						$total_tests = 	$total_tests + $n_tests;
				}
		 }

			 if($total_tests == 0)
				 return 0;
			 $progress =  ($tests_completed/$total_tests)*100;
				 return (int)$progress;			 
		 }
		
	 }
	
}
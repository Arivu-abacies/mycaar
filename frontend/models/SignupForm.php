<?php
namespace frontend\models;


use yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $confirm_password;
    public $company_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            [['password','confirm_password'], 'required'],
            ['company_id', 'required'],
            [['password','confirm_password'], 'string', 'min' => 6,'max'=>15],
			[['password','confirm_password'],'filter','filter'=>'trim'],
			[['confirm_password'],'compare','compareAttribute'=>'password','message'=>'Password do not match'],
        ];
    }
	
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
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
         if (!$this->validate()) { 
            return null;
        }       
        $user = new User();
        $user->username = $this->email;
        $user->email = $this->email;
        $user->c_id = $this->company_id; 		
        $user->role = 'user'; 		
        $user->password = $this->password;		
        $user->setPassword($user->password);
        $user->generatePasswordResetToken();
        $user->generateAuthKey();
		
		if($user->save())		
		 return $user;				
		else
		 return null;
       // return $user->save()?$user:null;
    }
	
	 public function sendEmail($password)
    {
        /* @var $user User */
		$model = new User();

        $user = $model::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

            if ($user) {
                $message = Yii::$app->mail->compose(['html' => 'signup-password-Send-text'], ['user' => $user,'password'=>$password])
                    ->setFrom([Yii::$app->params['supportEmail'] =>'MyCaar '])
                    ->setTo($this->email)
                    ->setSubject('Please Verified your Email');
                    
					 $message->getSwiftMessage()->getHeaders()->addTextHeader('MIME-version', '1.0\n');
					/*  $message->getSwiftMessage()->getHeaders()->addTextHeader('Content-Type', 'text/html'); */
					 $message->getSwiftMessage()->getHeaders()->addTextHeader('charset', ' iso-8859-1\n');
			 return  $message->send();
            }
        

        return false;
    }
	
}

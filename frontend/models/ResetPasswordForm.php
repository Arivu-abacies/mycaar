<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;
use common\models\User;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;

    /**
     * @var \common\models\User
     */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Password reset token cannot be blank.');
        }
        $this->_user = User::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new InvalidParamException('Wrong password reset token.');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6,'max'=>15],
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        return $user->save(false);
    }
	
	public function sendEmailResetSuccess()
    {  
	
		$userdetails = $this->_user;
		
	 $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $userdetails->email,
        ]);
			
        if (!$user) {
            return false;
        }
        
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
			$user->scenario = 'apply_forgotpassword';	
            if (!$user->save()) { 
                return false;
            }
        }
		
       $message = Yii::$app
            ->mail
            ->compose(
                ['html' => 'passwordResetSuccessMessage-html'],['user'=>$user]               
            )
            ->setFrom([Yii::$app->params['supportEmail'] => 'MyCaar '])
            ->setTo($userdetails->email)
            ->setSubject('MyCaar Reset-Password Success');
          			
		$message->getSwiftMessage()->getHeaders()->addTextHeader('MIME-version', '1.0\n');
	
		$message->getSwiftMessage()->getHeaders()->addTextHeader('charset', ' iso-8859-1\n');
		
		return $message->send();
    }	
}

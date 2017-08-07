<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\models\User;
use common\models\LoginForm;
use common\models\Company;
use common\models\UserProfile as Profile;
use common\models\Program;

use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;


class myAccessRule extends \yii\filters\AccessRule
{
    public function allows($action, $user, $request)
    {
        $isMatchAction = $this->matchAction($action);
        if ($isMatchAction
            && $this->matchRole($user)
            && $this->matchIP($request->getUserIP())
            && $this->matchVerb($request->getMethod())
            && $this->matchController($action->controller)
            && $this->matchCustom($action)
        ) {
            return $this->allow ? true : false;
        } else {
            return $this->allow && $isMatchAction ? false : null;
        }
    }
}


/**
 * Site controller
 */
class SiteController extends Controller
{
	
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
				'ruleConfig' => [
                  'class' =>   myAccessRule::className()
                ],
                'only' => ['logout', 'signup','index'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
						'denyCallback' => function ($rule, $action) {
								return $this->goHome();
							},
                    ],
                    [
                        'actions' => ['logout','index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
		//render the report of the user here
		$user = User::findOne(\Yii::$app->user->id);
		$enrolled = $user->getPrograms();
		$programs = [];
		$programs_id = "";
		
		foreach($enrolled as $tmprogramid){
			$programs_id[] = $tmprogramid->program_id;
		}
	
		$programs = Program::find()->where(['IN', 'program_id', $programs_id ])->orderBy('title')->all();
		
		/*  
		foreach($enrolled as $program){
			$programs[] = Program::findOne($program->program_id);
		}
	
		By ARIVU CLIENT Correction
		 */	
		$users[] = Profile::find()->where(['user_id'=>\Yii::$app->user->id])->one();	
		
		if(!Yii::$app->user->can('superadmin')){ 
			$company = Company::find()->where(['company_id'=>$user->c_id ])->one();	 
			
			if($company && $company->status == 1)
			   return $this->render('under_construction', [ 'company' => $company] );
		}
	
			return $this->render('home', [
				'programs' => $programs,
				'users' => $users,
			]);
		
    }

    /**
     * Logs in a user.
	 * check for both username/ email
     *
     * @return mixed
     */
    public function actionLogin($companyslug=false)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
			if(\Yii::$app->user->can("superadmin"))
				return $this->redirect(Yii::$app->urlManagerBackEnd->baseUrl);
            else return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,'companyslug'=>$companyslug
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup($slug)
    {
	 if($slug)
	  {
		$company = Company::find()->where(["slug" =>$slug])->one();
		if(!$company)
			 return $this->redirect('Login');
		 		
        $model = new SignupForm();
		$profile = new Profile();
		$profile->scenario = 'company_admin_user';		
		if(($model->load(Yii::$app->request->post())) && ($profile->load(Yii::$app->request->post())) && ($model->validate()) && ($profile->validate())) {			
            if ($user = $model->signup()) { 
			
				$auth = Yii::$app->authManager;
				$authorRole = $auth->getRole($user->role);
				$auth->assign($authorRole, $user->id); 		
				
				$profile->user_id = $user->id;			
				$profile->save();	
				$model->sendEmail($model->password);
							
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            } else {
					return $this->render('signup', ['model' => $model,'profile'=>$profile,"company"=>$company]);
			} 
        }

        return $this->render('signup', [
            'model' => $model,'profile'=>$profile,"company"=>$company
        ]);
	  } else {
		  return $this->redirect('Login');
	  }
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
		
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post())) {
			
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else { 
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
				
            }			   
        } 
        return $this->render('requestPasswordResetToken', ['model' => $model,]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
			
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');			
			$model->sendEmailResetSuccess();			
            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
	
	public function actionChangePassword()
    {		
		$model = Yii::$app->user->identity;
		$model->scenario = 'apply_changepassword';
		 if ($model->load(Yii::$app->request->post())) {			
			 $model->setPassword($model->new_password);			
			 if($model->save())
			 {
				 Yii::$app->getSession()->setFlash('Success', 'You have successfully change your password !!!.');
				$model->sendEmailChangePassword();					 
			 } else {
				 return $this->render('change_password', ['model' => $model]);				
			 }
			 
			 return $this->refresh();
		 }
		return $this->render('change_password', ['model' => $model]);	
	}
	
		
	
}

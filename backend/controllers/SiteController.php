<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use common\models\SiteMeta;
use common\models\SetPassword;
use yii\web\UploadedFile;

///////////////////////////////////////


//use common\models\User;
use common\models\Company;
use common\models\Program;
use common\models\ProgramEnrollment;






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
                'rules' => [
                    [
                        'actions' => ['login','error','test-mail','test-awareness','test-capability'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
					[
                        'actions' => ['reset-password', 'error'],
                        'allow' => true,
                    ],
					[
                        'actions' => ['pwdregister', 'error'],
                        'allow' => true,
                    ],
					[
                        'actions' => ['forgotten', 'error'],
                        'allow' => true,
                    ],
					[
                        'actions' => ['change-password', 'error'],
                        'allow' => true,
                    ],
					[
                        'actions' => ['sitemeta', 'error'],
                        'allow' => true,
                    ],
					[
                        'actions' => ['sitemeta-update', 'error'],
                        'allow' => true,
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
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
		return $this->redirect(Yii::$app->urlManagerFrontEnd->baseUrl);
/* 		$this->layout = "login";
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        } */
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
		return $this->redirect(Yii::$app->urlManagerFrontEnd->baseUrl);
        //return $this->goHome();
    }
	
	// Forgot Password Form Generation
	
	public function actionForgotten()
    {
		$this->layout = "login";
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();		
        if ($model->load(Yii::$app->request->post())) {					
			$userdetails = $model->Usernamecheck($model->username);		
			if($userdetails)
			{
			$model2 = User::find()->where(['id'=>$userdetails->id])->one();
			
			// password_reset_token field is generated
			$model2->generatePasswordResetToken();	
			$model2->scenario = 'apply_forgotpassword';		
			if($model2->save())
			{	
			  // Email Function is "Send Email to respective user"
			   $model2->sendEmailForgotPassword();				
			   Yii::$app->getSession()->setFlash('Success', 'Reset-Password Link Send to Your Email ID!!!.');
			   
			} else {
				Yii::$app->getSession()->setFlash('Error', 'Invalid Username Or Email ID, Please Try Again !!!.');
				 
			}
				return $this->render('forgotten_form', ['model' => $model]);
			} else {
				Yii::$app->getSession()->setFlash('Error', 'Invalid Username Or Email ID, Please Try Again !!!.');
				return $this->render('forgotten_form', ['model' => $model]);
			}
        } else {
            return $this->render('forgotten_form', ['model' => $model]);
        }
		
    }
	
	
	// Email-link through Reset-link
	
	public function actionResetPassword($token)
    {			
		if($token)
		{ 
			$ckuser = User::find()->where(['password_reset_token'=>$token])->one();				
			if($ckuser)
			{	
				$this->layout = "login";
				$model = new SetPassword();
				return $this->render('setpassword', ['model' => $model,'user_id'=>$ckuser->id]);
			} else { 
				return $this->redirect('login');
			}			
		} else { 		
			return $this->redirect('login');
		}
	}
	
	// Generate New Password For Forgot Link 
	
 	public function actionPwdregister()
    {		
		$this->layout = "login";		
		$model = new  SetPassword();			
        if ($model->load(Yii::$app->request->post())) {					
			$userid =  $model->id;									
			$model2 = User::findOne($userid);					
			if($model2)
			{ 
			$model2->setPassword($model->password_hash);				
			$model2->removePasswordResetToken();	
			$model2->scenario = 'apply_setpassword';
			
			if($model2->save())
				Yii::$app->getSession()->setFlash('Success', 'Password Set successfully, Please Login Once!!!.');
			else 
				Yii::$app->getSession()->setFlash('Error', 'Please Try Again!!!.');	
			} else {
			 Yii::$app->getSession()->setFlash('Error', 'Please Try Again!!!.');
			}
			return $this->redirect('login');
		}
		else{			
			return $this->redirect('reset-password');
		}
		
	} 
	
	// Change Password Form Generation
	
	public function actionChangePassword()
    {		
		//$model = new SetPassword();
		$model = Yii::$app->user->identity;
		$model->scenario = 'apply_changepassword';
		 if ($model->load(Yii::$app->request->post())) {			
			 $model->setPassword($model->new_password);			
			 if($model->save())
			 {
				 Yii::$app->getSession()->setFlash('Success', 'You have successfully change your password !!!.');
				 $model->sendEmailChangePassword();	
			 } 
		}
		return $this->render('change_password', ['model' => $model]);	
	}
	
	
	public function actionSitemeta()
	{	
		$model = new SiteMeta();
		
		//// Right Side Image Values 
		$right_logo = SiteMeta::find()->where(['meta_key'=>'right-side-logo'])->one();
		$current_right_image = $right_logo->meta_value;	
		
		//// Left Side Image Values 		
		$left_logo = SiteMeta::find()->where(['meta_key'=>'left-side-logo'])->one();
		$current_left_image = $left_logo->meta_value;
		
	  if ($model->load(Yii::$app->request->post())) {	
		
			//// Right Side Image Uploaded Function 
			$right_logo->rightsidelogo = UploadedFile::getInstance($model, 'rightsidelogo');		
			if(!empty($right_logo->rightsidelogo)){   
				if(!$right_logo->uploadrightImage())				
					return;
				 else
					$right_logo->meta_value = $right_logo->rightsidelogo;	
				
			} else {
				$right_logo->meta_value = $current_right_image;
			}								
			$right_logo->save();	
			
			//// End Up Right Side Image Uploaded Function 
			
			//// Left Side Image Uploaded Function 			
			$left_logo->leftsidelogo = UploadedFile::getInstance($model, 'leftsidelogo');		
			if(!empty($left_logo->leftsidelogo)){   
				if(!$left_logo->uploadleftImage())				
					return;
				else
					$left_logo->meta_value = $left_logo->leftsidelogo;				
			} else {
				$left_logo->meta_value = $current_left_image;
			}	
			$left_logo->save();	
			//// End Up Left Side Image Uploaded Function 
			
			 Yii::$app->getSession()->setFlash('Success', 'Logo Changed Successfully!!!.');
			
		}		
			return $this->render('sitemeta', ['left' =>$left_logo,'right'=>$right_logo,'model'=>$model]);	
			
	}
	
	
	
	public function actionTestAwareness()
    {
		echo "Started Running";
		echo "<br>";
		
		$connection = \Yii::$app->db;
		$model = $connection->createCommand('SELECT u.id as user_id FROM `company` c,`user` u, `program` p, `program_enrollment` pe where c.company_id = u.c_id and c.company_id = p.company_id and c.company_id = 1 and p.program_id = pe.program_id and u.id = pe.user_id and p.program_id =1');
		$temp_all_users = $model->queryAll();
		
		$all_users = array();
		foreach ($temp_all_users as $key=>$tmp) {
			$all_users[$key] = $tmp['user_id'];
		
		}


		/* echo "<pre>";
		print_r($all_users);		
		exit; */
		
		$model2 = $connection->createCommand('SELECT aq.aq_id, aq.unit_id, aq.question, aq.answer FROM `module` m, `unit` u, `awareness_question` aq where m.module_id = u.module_id and u.unit_id = aq.unit_id and m.module_id = 11 and m.program_id = 1');
		$awareness_question = $model2->queryAll();
		
		$all_awareness_question = array();
		foreach ($awareness_question as $key=>$tmp1) {
			$all_awareness_question[$key] = $tmp1['aq_id'];
		
		}
		
		/* echo "<pre>";
		print_r($all_awareness_question);		
		exit; */
		
		
		
		
		$model22 = $connection->createCommand('SELECT * FROM `unit` where module_id = 11');
		$units = $model22->queryAll();
		
		
		$all_unit = array();
		foreach ($units as $key=>$tmp3) {
			$all_unit[$key] = $tmp3['unit_id'];
		
		}
		
		/* echo "<pre>";
		print_r($all_unit);
		exit; */
		
		foreach($all_users as $user_id)
		{
			 foreach($all_awareness_question as $aware_ques)
			{
				//echo $user_id."___".$aware_ques."<br>";
				
				$model3 = $connection->createCommand('SELECT * FROM `awareness_question` WHERE `aq_id` = '.$aware_ques );
				$check_question = $model3->queryOne();
				
				//  echo "<pre>";
				//print_r($check_question['answer']);   
				
				//exit;
				
				
				$model4 = $connection->createCommand('SELECT * FROM `awareness_answer` WHERE `question_id` = '.$aware_ques.' AND `user_id` = '.$user_id);
				
				$check_answer = $model4->queryOne();
				
				//echo "<pre>";
				//print_r($check_answer); 
				
				if(!empty($check_answer))
				{
					
					$model5 = $connection->createCommand("UPDATE `awareness_answer` SET  `answer`= '$check_question[answer]' WHERE `aa_id`= '$check_answer[aa_id]'");
					$update_answer = $model5->query();
					
				} else 
				{
					
					$model6 = $connection->createCommand("INSERT INTO `awareness_answer`(`question_id`, `user_id`, `answer` ) VALUES ('$aware_ques','$user_id','$check_question[answer]')");
					$insert_answer = $model6->query();
				}
				
			}
			 
			
			foreach ($all_unit as $unit_id) {
					
				$model7 = $connection->createCommand('SELECT * FROM `unit_report` WHERE `unit_id` = '.$unit_id.' AND `student_id` ='.$user_id);
				
				$check_unit_report = $model7->queryOne();
				
				if(!empty($check_unit_report))
				{
					
					$model8 = $connection->createCommand("UPDATE `unit_report` SET  `awareness_progress`= '100' WHERE `report_id`= '$check_unit_report[report_id]'");
					$update_answer = $model8->query();
					
				} else 
				{
					
					$model9 = $connection->createCommand("INSERT INTO `unit_report`(`unit_id`, `student_id`, `awareness_progress`) VALUES ('$unit_id','$user_id','100')");
					$insert_answer = $model9->query();
				}
			}
			
		}
		
		
	}




public function actionTestCapability()
    {
		echo "Started Running";
		echo "<br>";
		
		$connection = \Yii::$app->db;
		$model = $connection->createCommand('SELECT u.id as user_id FROM `company` c,`user` u, `program` p, `program_enrollment` pe where c.company_id = u.c_id and c.company_id = p.company_id and c.company_id = 1 and p.program_id = pe.program_id and u.id = pe.user_id and p.program_id =1');
		$temp_all_users = $model->queryAll();
		
		$all_users = array();
		foreach ($temp_all_users as $key=>$tmp) {
			$all_users[$key] = $tmp['user_id'];
		
		}


		/* echo "<pre>";
		print_r($all_users);		
		exit; */
		
		$model2 = $connection->createCommand('SELECT cq.cq_id, cq.unit_id, cq.question, cq.answer FROM `module` m, `unit` u, `capability_question` cq where m.module_id = u.module_id and u.unit_id = cq.unit_id and m.module_id = 11 and m.program_id = 1');
		$capability_question = $model2->queryAll();
		
		$all_capability_question = array();
		foreach ($capability_question as $key=>$tmp1) {
			$all_capability_question[$key] = $tmp1['cq_id'];
		
		}
		
		/* echo "<pre>";
		print_r($all_capability_question);		
		exit; */
		
		
		
		
		$model22 = $connection->createCommand('SELECT * FROM `unit` where module_id = 11');
		$units = $model22->queryAll();
		
		
		$all_unit = array();
		foreach ($units as $key=>$tmp3) {
			$all_unit[$key] = $tmp3['unit_id'];
		
		}
		
		/* echo "<pre>";
		print_r($all_unit);
		exit; */
		
		foreach($all_users as $user_id)
		{
			 foreach($all_capability_question as $cap_ques)
			{
				//echo $user_id."___".$cap_ques."<br>";
				
				$model3 = $connection->createCommand('SELECT * FROM `capability_question` WHERE `cq_id` = '.$cap_ques );
				$check_question = $model3->queryOne();
				
				//  echo "<pre>";
				//print_r($check_question['answer']);   
				
				//exit;
				
				
				$model4 = $connection->createCommand('SELECT * FROM `capability_answer` WHERE `question_id` = '.$cap_ques.' AND `user_id` = '.$user_id);
				
				$check_answer = $model4->queryOne();
				
				//echo "<pre>";
				//print_r($check_answer); 
				
				if(!empty($check_answer))
				{
					
					$model5 = $connection->createCommand("UPDATE `capability_answer` SET  `answer`= '$check_question[answer]' WHERE `ca_id`= '$check_answer[ca_id]'");
					$update_answer = $model5->query();
					
				} else 
				{
					
					$model6 = $connection->createCommand("INSERT INTO `capability_answer`(`question_id`, `user_id`, `answer` ) VALUES ('$cap_ques','$user_id','$check_question[answer]')");
					$insert_answer = $model6->query();
				}
				
			}
			 
			
			foreach ($all_unit as $unit_id) {
					
				$model7 = $connection->createCommand('SELECT * FROM `unit_report` WHERE `unit_id` = '.$unit_id.' AND `student_id` ='.$user_id);
				
				$check_unit_report = $model7->queryOne();
				
				if(!empty($check_unit_report))
				{
					if(!empty($check_unit_report['cap_done_by']))
					{
						$cap_done_by = $check_unit_report['cap_done_by']; 
					} else {
						$cap_done_by = 3;
					}
					
					$model8 = $connection->createCommand("UPDATE `unit_report` SET  `capability_progress`= '100',`cap_done_by` = '$cap_done_by'  WHERE `report_id`= '$check_unit_report[report_id]'");
					$update_answer = $model8->query();
					
				} else 
				{
					
					$model9 = $connection->createCommand("INSERT INTO `unit_report`(`unit_id`, `student_id`, `capability_progress`,`cap_done_by`) VALUES ('$unit_id','$user_id','100','3')");
					$insert_answer = $model9->query();
				}
			}
			
		}
		
		
	}	

	
}

<?php

namespace backend\modules\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\User;
use common\models\UserProfile as Profile;
use common\models\ImportFile;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use common\models\Role;
use common\models\Division;
use common\models\Location;
use common\models\State;
use common\models\QuickEmail;
use common\models\MyCaar;
use yii\helpers\ArrayHelper;


/**
 * ImportuserController implements the CRUD actions for Company model.
 */
class ImportuserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),               
            ],
			'access' => [
				'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['importexcel'],
                        'allow' => true,
						'roles' => ['company_admin']
                    ],					 
				],
			]
			
        ];
    }

   public function actionImportexcel()
    {			
 
			   $model = new ImportFile();			  
			 if($model->load(Yii::$app->request->post())){
				
		///GET VAlues and Stored in Array with Key //////	 
		
			$getlocationname = ArrayHelper::map(Location::find()->select(['location_id','name'])->where(['company_id'=>$model->company_id])->asArray()->all(), 'location_id', 'name');
			 
			$getdivisionname = ArrayHelper::map(Division::find()->select(['division_id','title'])->where(['company_id'=>$model->company_id])->asArray()->all(), 'division_id', 'title');
			 
			$getrolename = ArrayHelper::map(Role::find()->select(['role_id','title'])->where(['company_id'=>$model->company_id])->asArray()->all(), 'role_id', 'title');
			 
			$getstatename = ArrayHelper::map(State::find()->select(['state_id','name'])->where(['company_id'=>$model->company_id])->asArray()->all(), 'state_id', 'name');
			
				/// Upload Excel File Read The File Function  
				
		        $model->upfile = UploadedFile::getInstance($model, 'upfile');				
				$inputFiles = $model->upfile->tempName ;					
				  try{
					 $objPHPExcel = new \PHPExcel();
					 $inputFileType = \PHPExcel_IOFactory::identify($inputFiles);
					 $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
					 $objPHPExcel = $objReader->load($inputFiles);
					 
				  } catch (Exception $ex) {             
					 die('Error in File Formate');
				  }						  
				  $sheet = $objPHPExcel->getSheet(0);
				  $highestRow = $sheet->getHighestRow();	
				  $highestColumn = $sheet->getHighestColumn();				
				  $error_report = [];	
				  
				 //$row is start 2 because first row assigned for heading. 
				 
				 for($row=2; $row<=$highestRow; ++$row)
				 {                  				 
					 $rowData = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row,NULL,TRUE,FALSE);	
					
						if(empty($rowData[0][0]))
						{
							continue;
						}
					//save to User  table.
					 $usertable = new User();
					if(\Yii::$app->user->can('company_admin')) {
						$usertable->scenario = 'update_by_admin';
						}						
						 $profiletable = new Profile();	 
						 $usertable->email = $rowData[0][0];					
						 $usertable->username = $usertable->email;					 
						 $usertable->generateAuthKey();
						 $usertable->generatePasswordResetToken();						 
						 $usertable->c_id = $model->company_id; 					
						 $usertable->role = 'user';	

		/**  Random Password Generator **/
				
				//Random Password Generation For MyCaar Common models

			 $usertable->password = MyCaar::getRandomPassword();
			 $usertable->setPassword($usertable->password);	 
		//***** Role check and Create Function *** //	
		
			$getroleid = array_search(strtolower(trim($rowData[0][1])), array_map('strtolower', $getrolename));
			  if($getroleid)
				{
					$profiletable->role = $getroleid;
				} else {
					$rolenew = new Role();	
					$rolenew->title = trim($rowData[0][1]);
					$rolenew->company_id = $usertable->c_id;
					$rolenew->save();
					
				$newrole = array( $rolenew->role_id =>trim($rowData[0][1]));
				$profiletable->role = $rolenew->role_id;
				$getrolename = $getrolename + $newrole;				
				}
				
				
		//***** Division check and Create Function *** ///		
								
			  $getdivisionid = array_search(strtolower(trim($rowData[0][3])), array_map('strtolower', $getdivisionname));
			  if($getdivisionid)
				{
					$profiletable->division = $getdivisionid;
				} else {
					$divisionnew = new Division();	
					$divisionnew->title = trim($rowData[0][3]);
					$divisionnew->company_id = $usertable->c_id;
					$divisionnew->save();
					
				$newdivision = array( $divisionnew->division_id =>trim($rowData[0][3]));
				$profiletable->division = $divisionnew->division_id;
				$getdivisionname = $getdivisionname + $newdivision;				
				}
				
	   //***** Location check and Create Function *** ///						 						 
			
			  $getlocationid = array_search(strtolower(trim($rowData[0][4])), array_map('strtolower', $getlocationname));
			  if($getlocationid)
				{
					$profiletable->location = $getlocationid;
				} else {
					$locationnew = new Location();	
					$locationnew->name = trim($rowData[0][4]);
					$locationnew->company_id = $usertable->c_id;
					$locationnew->save();
					
				$newlocation = array( $locationnew->location_id =>trim($rowData[0][4]));
				$profiletable->location = $locationnew->location_id;
				$getlocationname = $getlocationname + $newlocation;				
				}
				
				
	   //***** State check and Create Function *** ///			
	   
				 $getstateid = array_search(strtolower(trim($rowData[0][7])), array_map('strtolower', $getstatename));
			  if($getstateid)
				{
					$profiletable->state = $getstateid;
				} else {
					$statenew = new State();	
					$statenew->name = trim($rowData[0][7]);
					$statenew->company_id = $usertable->c_id;
					$statenew->save();
					
				$newstate = array( $statenew->state_id =>trim($rowData[0][7]));
				$profiletable->state = $statenew->state_id;
				$getstatename = $getstatename + $newstate;				
				}		
						
			//////////////////////////////
			
						 $profiletable->employee_number = $rowData[0][2];
						 $profiletable->firstname = $rowData[0][5];
						 $profiletable->lastname = $rowData[0][6];			
					 if($usertable->save())
					 {			 
						$auth = Yii::$app->authManager;
						$authorRole = $auth->getRole($usertable->role);
						$auth->assign($authorRole, $usertable->id); 
						$profiletable->user_id =  $usertable->id;							
					 	$profiletable->save();  
												
			/******** Send Mail Function to DataBase **********/
				
				// Email Message is saved in database
				$quickemail = new QuickEmail();	
				$quickemail->c_id = $model->company_id;
				$quickemail->user_id = $usertable->id;
				$quickemail->to_email = $rowData[0][0];
				$quickemail->from_email = "info_notification@gmail.com";
				$quickemail->subject = "Please Verified your Email";
				$loginLink = Yii::$app->urlManagerFrontEnd->createAbsoluteUrl(['site/login']);
				$resetLink = Yii::$app->urlManagerFrontEnd->createAbsoluteUrl(['site/reset-password', 'token' => $usertable->password_reset_token]);
				$arr = array('password' => $usertable->password, 'loginLink' => $loginLink, 'resetLink' => $resetLink);
				$quickemail->message = json_encode($arr);
				$quickemail->status = 0;			
				$quickemail->save();	
				
		   /******** Send Mail Function to Each User **********/		
		  
					 } else 
					 {						
						 $errors = $usertable->errors;
						 reset($errors);
						 $listerror = current($errors);
						 //$error_report[] = $usertable-->email." -- ".$listerror[0];
						 $error_report[] = "No.".$row." -- ".$usertable->email." -- ".$listerror[0];
					 }					 
				 }				
				if(isset($error_report) && empty($error_report))
				{
					 Yii::$app->getSession()->setFlash('Success', 'Import User added Sucessfully!!!.');
				}
				else{
					 Yii::$app->getSession()->setFlash('Error', 'User Details Failed To Imported Following Reasons !!!.');
					 Yii::$app->getSession()->setFlash('Error-data', $error_report); 
				}				
				return $this->render('upload_form', ['model' => $model ]);			
		}else {
				return $this->render('upload_form', ['model' => $model ]);
			  }
    }	

}

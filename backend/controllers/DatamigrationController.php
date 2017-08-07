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

/**
 * Site controller
 */
class DatamigrationController extends Controller
{
	public function fetch($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$data = curl_exec ($ch);
		curl_close ($ch);
		return $output = json_decode($data);
	}
	
	public function actionFetchLocation()
    {
		
		$url = 'http://mycaar.com.au/wp-content/data_extract.php?type=location';
		$locations = $this->fetch($url);
		foreach( $locations  as $location  )
		{
			$new = new \common\models\Location();
			$new->name = $location;
			$new->company_id = 1;
			$new->save();
		}
	
    }
	
	
	
	public function actionFetchDivision()
    {
		
		$url = 'http://mycaar.com.au/wp-content/data_extract.php?type=division';
		$divisions = $this->fetch($url);
		foreach( $divisions  as $division  )
		{
			$new = new \common\models\Division();
			$new-> title = $division;
			$new->company_id = 1;
			$new->save();
		}
	
    }
	
	
	public function actionFetchState()
    {
		
		$url = 'http://mycaar.com.au/wp-content/data_extract.php?type=state';
		$states = $this->fetch($url);
		foreach( $states  as $state  )
		{
			$new = new \common\models\State();
			$new->name = $state;
			$new->company_id = 1;
			$new->save();
		}
	
    }

	public function actionFetchRole()
    {
		
		$url = 'http://mycaar.com.au/wp-content/data_extract.php?type=role';
		$roles = $this->fetch($url);
		foreach( $roles  as $role  )
		{
			$new = new \common\models\Role();
			$new->title = $role;
			$new->company_id = 1;
			$new->save();
		}
	
    }
	
	public function actionFetchUser()
    {

		$url = 'http://mycaar.com.au/wp-content/data_extract.php?type=user';
		$users = $this->fetch($url);
		$password = "reset123";
		foreach( $users  as $user  )
		{	
			$new_user = new \common\models\User();
			$new_user->username = $user->email; 
			$new_user->email = $user->email; 			
			$new_user->setPassword($password);
			$new_user->generateAuthKey();
			$new_user->generatePasswordResetToken();
			$new_user->c_id = 1;
			$new_user->role = 'user';
		
			if($new_user->save())
			{ 			
			 if($user->accesslevel == "subscriber")
				$rolename = "user";
			 else if($user->accesslevel == "assessor")
			 	$rolename = "assessor";
			 else if($user->accesslevel ==  "administrator")	
				$rolename = "company_admin";
			
			 $auth = Yii::$app->authManager;
			 $authorRole = $auth->getRole($rolename);
			 $auth->assign($authorRole, $new_user->id); 
				
			if(!empty($user->division) && ($user->division !=0) )	
				$division = \common\models\Division::find()->select('division_id')->where(['title'=>$user->division,'company_id'=>1])->one()->division_id;
			else 
				$division = "";	
			
			if(!empty($user->role) && ($user->role !=0))	
				$role = \common\models\Role::find()->select('role_id')->where(['title'=>$user->role,'company_id'=>1])->one()->role_id;
			else 
				$role = "";	
			
			if(!empty($user->state) && ($user->state !=0))	
				$state = \common\models\State::find()->select('state_id')->where(['name'=>$user->state,'company_id'=>1])->one()->state_id;
			else
				$state = "";	
			
			if(!empty($user->location) && ($user->location !=0))	
				$location = \common\models\Location::find()->select('location_id')->where(['name'=>$user->location,'company_id'=>1])->one()->location_id;
			else
				$location = "";
			
			$new_userprofile = new \common\models\UserProfile();
			$new_userprofile->user_id = $new_user->id;
			$new_userprofile->firstname = $user->firstname;
			$new_userprofile->lastname = $user->lastname; 
			$new_userprofile->division = $division; 
			$new_userprofile->role = $role; 
			$new_userprofile->state = $state; 
			$new_userprofile->location = $location; 
			$new_userprofile->save();
			
		/******** Send Mail Function to DataBase **********/
				
				// Email Message is saved in database
				$quickemail = new \common\models\QuickEmail();	
				$quickemail->c_id = 1;
				$quickemail->user_id = $new_user->id;
				$quickemail->to_email = $user->email;
				$quickemail->from_email = "info_notification@gmail.com";
				$quickemail->subject = "Please Verified your Email";
				$loginLink = Yii::$app->urlManagerFrontEnd->createAbsoluteUrl(['site/login']);
				$resetLink = Yii::$app->urlManagerFrontEnd->createAbsoluteUrl(['site/reset-password', 'token' => $new_user->password_reset_token]);
				$arr = array('password' => $password, 'loginLink' => $loginLink, 'resetLink' => $resetLink);
				$quickemail->message = json_encode($arr);
				$quickemail->status = 0;			
				$quickemail->save();	
				
		   /******** Send Mail Function to Each User **********/	
		   
			
			
			}
				
		} 
		echo "<pre>";
		
    }
	
	
	public function actionFetchProgram()
    {
		
		$url = 'http://mycaar.com.au/wp-content/data_extract.php?type=program';
		$programs = $this->fetch($url);
		
		foreach( $programs  as $program  )
		{
			$new = new \common\models\Program();
			$new->title = $program->title;
			$new->company_id = 1;
			$new->description = $program->description;
			if($new->save())
			{
				foreach( $program->enrollments as $program_enrollment)
				{
					
					$user = \common\models\User::find()->select('id')->where(['email'=>$program_enrollment,'c_id'=>1])->one();
					
					if($user)
					{
						$ProgramEnrollment = \common\models\ProgramEnrollment::find()->where(["program_id"=>$new->program_id,"user_id"=>$user->id])->one();
						if($ProgramEnrollment == null)
							$ProgramEnrollment = new \common\models\ProgramEnrollment();
						$ProgramEnrollment->program_id = $new->program_id; 
						$ProgramEnrollment->user_id = $user->id;							
						$ProgramEnrollment->save();
						
					}
				}
			}
		}
	
    }
	
	
	public function actionFetchModule()
    {
		
		$url = 'http://mycaar.com.au/wp-content/data_extract.php?type=module';
		$modules = $this->fetch($url);

		foreach( $modules  as $module  )
		{
			$new = new \common\models\Module();

			$program = \common\models\Program::find()->select('program_id')->where(['title'=>$module->program[0],'company_id'=>1])->one();
		 if($program)
		  {
			$new->program_id = $program->program_id; 
			$new->title = $module->title;
			$new->short_description = $module->short_description;
			$new->featured_video_url = $module->featured_video_url;
			$new->featured_image = $module->featured_image;
			$new->detailed_description = $module->detailed_description;
			$new->language = $module->language;
			$new->course_start_date = $module->course_start_date;
			$new->course_end_date = $module->course_end_date;
			$new->enrl_start_date = $module->enrl_start_date;
			$new->enrl_end_date = $module->enrl_end_date;
			
			if($module->status == "publish")
				$new->status = 1;
			else
				$new->status = 0;
			
			$new->is_course_open_anytime = $module->is_course_open_anytime;
			$new->is_enrlmnt_open_anytime = $module->is_enrlmnt_open_anytime;
			$new->module_order = $module->module_order;
			$new->unique_key = $module->unique_key ;
			
			$new->save();
		  }
		}
	 
    }
	
	
	public function actionFetchUnit()
    {
		
		$url = 'http://mycaar.com.au/wp-content/data_extract.php?type=unit';
		$units = $this->fetch($url);

		foreach( $units  as $unit  )
		{
			$new = new \common\models\Unit();

			$module = \common\models\Module::find()->select('module_id')->where(['unique_key'=>$unit->module_unique_key])->one();
		 if($module)
		  {
			$new->module_id = $module->module_id; 
			$new->title = $unit->title;
		
			if($unit->status == "publish")
				$new->status = 1;
			else
				$new->status = 0;
		
			$new->auto_reset_period = "";
			$new->show_learning_page = 0;
			$new->unit_order = $unit->unit_order;
			$new->unique_key = $unit->unique_key; 
			if($new->save())
			{
				$element = new \common\models\UnitElement();
				$element->unit_id = $new->unit_id;
				$element->element_type = "page";
				$element->element_order = 1;
				$element->content = '{"html":"<form-template>\n\t<fields>\n\t</fields>\n</form-template>"}';
				$element->save();
				//aw_dat
				$element = new \common\models\UnitElement();
				$element->unit_id = $new->unit_id;
				$element->element_type = "aw_data";
				$element->element_order = 1;
				$element->content ='<form-template><fields></fields></form-template>';
				$element->save();
				//cp_dat
				$element = new \common\models\UnitElement();
				$element->unit_id = $new->unit_id;
				$element->element_type = "cap_data";
				$element->element_order = 1;
				$element->content ='<form-template><fields></fields></form-template>';
				$element->save();
			}
		  }
		}
	 
    }
	
	public function actionFetchCapability()
    {
		
		$url = 'http://mycaar.com.au/wp-content/data_extract.php?type=capability';
		$capability = $this->fetch($url);
		$get_unique_key  = []; 	
		foreach( $capability  as $key=>$capable )
		{
			
		   if(in_array($capable->unit_unique_key,$get_unique_key ))
		   {
				continue;
		   } else 
		   {				
				array_push($get_unique_key,$capable->unit_unique_key);
				$unit = \common\models\Unit::find()->select('unit_id')->where(['unique_key'=>$capable->unit_unique_key])->one();
		
			if($unit)
			{
				$content = "<form-template><fields>";
				foreach( $capable->title  as $key=>$title )
				{
					$new_capability_question = new \common\models\CapabilityQuestion();
				
					$new_capability_question->unit_id = $unit->unit_id;
					$new_capability_question->question = htmlentities($title, ENT_QUOTES, 'UTF-8');
					$new_capability_question->description = htmlentities($capable->description, ENT_QUOTES, 'UTF-8');
					$new_capability_question->answer = "yes";
					$new_capability_question->order_id = $key;
					
					if($new_capability_question->save())
					{
						$content .= "<field type='radio-group' description='".$new_capability_question->question."' label='Single Choice' class='radio-group' name='radio-group-".$new_capability_question->cq_id."' src='false'><option label='yes' value='yes' selected='true'>'yes'</option><option  label='no' value='no'>'no'</option></field>";
						
					}
						
				}	

				$content .= "</fields></form-template>";
					
				$unitelement = \common\models\UnitElement::find()->where(['unit_id'=>$unit->unit_id,"element_type"=>"cap_data"])->one();
					
					if($unitelement)
					{
					$unitelement->unit_id = $unit->unit_id;
					$unitelement->element_type = "cap_data";
					$unitelement->element_order = 1;
					//$unitelement->content = $content;						
					$unitelement->content = str_replace("&amp;","and",$content);						
					$unitelement->save();
					}					
			}	
		  }	
		}
		
	}
	
	public function actionFetchAwareness()
    {
		
		$url = 'http://mycaar.com.au/wp-content/data_extract.php?type=aware';
		$awareness = $this->fetch($url);

		foreach( $awareness  as $awaren )
		{
			$unit = \common\models\Unit::find()->select('unit_id')->where(['unique_key'=>$awaren->unit_unique_key])->one();
			unset($type);
			if($unit)
			{
				if(!empty($awaren->aware_questions))
				{
					$content = "<form-template><fields>";
					
					foreach($awaren->aware_questions as $question)
					{
						if($question->type == "radio" )
							$type = "radio-group";
						else if($question->type == "checkbox" )
							$type = "checkbox-group";
						else if($question->type == "file" )
							$type = "filedownload";
					
						if(!empty($type))
						{
							$new_awareness_question = new \common\models\AwarenessQuestion();
							
							$new_awareness_question->unit_id = $unit->unit_id;
							$new_awareness_question->question = htmlentities($question->title, ENT_QUOTES, 'UTF-8');
							$new_awareness_question->description = htmlentities($question->description, ENT_QUOTES, 'UTF-8');
							$new_awareness_question->src = $question->source;				
							$new_awareness_question->question_type = $type; 
							$new_awareness_question->answer = "";
							$new_awareness_question->order_id = $question->question_order;
							
							if($new_awareness_question->save(false))
							{
								
							$content .= "<field type='".$type."' description='".$new_awareness_question->description."' label='".$new_awareness_question->question."' class='".$type."' name='".$type."-".$new_awareness_question->aq_id."' src='".$question->source."'>";
								
								$optionsvalues = [];
								if($question->options)
								{							
									foreach($question->options as $option )
									{
									  $new_awareness_option = new \common\models\AwarenessOption();						
									  $new_awareness_option->question_id = $new_awareness_question->aq_id;
									  $new_awareness_option->answer = htmlentities($option, ENT_QUOTES, 'UTF-8');
									  $new_awareness_option->save();
									  
									  if(is_array($question->right_answer))
									  {
										if(in_array($option, $question->right_answer))										
										{
											$optionsvalues[] = $new_awareness_option->option_id; 
											$content .= "<option option_id='".$new_awareness_option->option_id."'  label='".$new_awareness_option->answer."' value='".$new_awareness_option->answer."' selected='true' >'".$new_awareness_option->answer."'</option>";
										}
										else
										{
											$content .= "<option option_id='".$new_awareness_option->option_id."'  label='".$new_awareness_option->answer."' value='".$new_awareness_option->answer."' >'".$new_awareness_option->answer."'</option>"; 
										}
									  }
									  else 
									  {
										if($question->right_answer ==  $option)
										{
										  $optionsvalues[] = $new_awareness_option->option_id; 
										  $content .= "<option option_id='".$new_awareness_option->option_id."'  label='".$new_awareness_option->answer."' value='".$new_awareness_option->answer."' selected='true' >'".$new_awareness_option->answer."'</option>";
										}
										else
										{
											$content .= "<option option_id='".$new_awareness_option->option_id."'  label='".$new_awareness_option->answer."' value='".$new_awareness_option->answer."' >'".$new_awareness_option->answer."'</option>"; 
										}
									  }
									  
									  
									  
									}							
								}
								
								$rightanswer = implode("_",$optionsvalues);
								$new_awareness_question->answer = $rightanswer; 								
								$new_awareness_question->save();
								
								$content .= "</field>";
							}										
						}
					}	
					
					$content .= "</fields></form-template>";
					
					$unitelement = \common\models\UnitElement::find()->where(['unit_id'=>$unit->unit_id,"element_type"=>"aw_data"])->one();
					
					if($unitelement)
					{
					$unitelement->unit_id = $unit->unit_id;
					$unitelement->element_type = "aw_data";
					$unitelement->element_order = 1;
					$unitelement->content = str_replace("&amp;","and",$content);						
					$unitelement->save();
					}
					
				}	
			}
		}
	 
    }
	
	public function actionFetchAwarenessTestCompleted()
    {
		$programlist = \common\models\Program::find()->select('program_id')->where(['company_id'=>1])->all();
		
		foreach( $programlist  as $program )
		{
			$modulelist = \common\models\Module::find()->select(['program_id','module_id','unique_key'])->where(['program_id'=>$program->program_id])->all();
				
			foreach( $modulelist as $module )
			{
				$program_enrolled_user_list = \common\models\ProgramEnrollment::find()->select('user_id')->where(['program_id'=>$module->program_id])->all();
				
				foreach( $program_enrolled_user_list as $userlist)
				{
					$user = \common\models\User::find()->select(['email','id'])->where(['id'=>$userlist->user_id])->one();
					$user_email = $user->email;
					$module_unique_key = $module->unique_key;
					
						$url = "http://mycaar.com.au/wp-content/data_extract.php?type=complete&user=$user_email&course=$module_unique_key";
						
					$unit_key_list = $this->fetch($url); 
					
					if(!empty($unit_key_list))
					{
						foreach( $unit_key_list as $unit_key )
						{
							$unit = \common\models\Unit::find()->select('unit_id')->where(['unique_key'=>$unit_key,'module_id'=>$module->module_id])->one();
						
							if(!empty($unit))
							{
								$awareness_question_list = \common\models\AwarenessQuestion::find()->select(['aq_id','answer'])->where(['unit_id'=>$unit->unit_id])->all();
								
								if(!empty($awareness_question_list))
								{
									foreach( $awareness_question_list as $aware )
									{
										$new_awar_ans = new \common\models\AwarenessAnswer();
										$new_awar_ans->question_id =  $aware->aq_id;
										$new_awar_ans->user_id =  $user->id;
										$new_awar_ans->answer =  $aware->answer;
										$new_awar_ans->save();
										
									}
									
										$new_unit_report = \common\models\UnitReport::find()->where(['unit_id'=>$unit->unit_id,'student_id'=>$user->id])->one();
										
										if($new_unit_report == null)
											$new_unit_report = new \common\models\UnitReport();
										
										$new_unit_report->unit_id =  $unit->unit_id;
										$new_unit_report->student_id =  $user->id;
										$new_unit_report->awareness_progress =  100;
										$new_unit_report->save();
								}
							}
							
							
						}
					
					} 
					
				}
				
			}
		}
		
		echo "Completed";
	
	}	
	
	
	public function actionFetchCapabilityTestCompleted()
    {
		$programlist = \common\models\Program::find()->select('program_id')->where(['company_id'=>1])->all();
		
		foreach( $programlist  as $program )
		{
			$modulelist = \common\models\Module::find()->select(['program_id','module_id','unique_key'])->where(['program_id'=>$program->program_id])->all();
				
			foreach( $modulelist as $module )
			{
				$program_enrolled_user_list = \common\models\ProgramEnrollment::find()->select('user_id')->where(['program_id'=>$module->program_id])->all();
				
				foreach( $program_enrolled_user_list as $userlist)
				{
					$user = \common\models\User::find()->select(['email','id'])->where(['id'=>$userlist->user_id])->one();
					$user_email = $user->email;
					$module_unique_key = $module->unique_key;
					
					$url = "http://mycaar.com.au/wp-content/data_extract.php?type=complete_capability&user=$user_email&course=$module_unique_key";
						
					$unit_key_list = $this->fetch($url); 
					
					if(!empty($unit_key_list))
					{
						
						foreach( $unit_key_list as $unit_key )
						{
						
						$unit = \common\models\Unit::find()->select('unit_id')->where(['unique_key'=>$unit_key->unit,'module_id'=>$module->module_id])->one();
							
							if(!empty($unit))
							{
								$capability_question_list = \common\models\CapabilityQuestion::find()->select(['cq_id','answer'])->where(['unit_id'=>$unit->unit_id])->all();
								
								if(!empty($capability_question_list))
								{
									foreach( $capability_question_list as $caps )
									{
										$new_cap_ans = new \common\models\CapabilityAnswer();
										$new_cap_ans->question_id =  $caps->cq_id;
										$new_cap_ans->user_id =  $user->id;
										$new_cap_ans->answer =  $caps->answer;
										$new_cap_ans->save();
										
									}
									
										$new_unit_report = \common\models\UnitReport::find()->where(['unit_id'=>$unit->unit_id,'student_id'=>$user->id])->one();
										
										if($new_unit_report == null)
											$new_unit_report = new \common\models\UnitReport();
										
										$new_unit_report->unit_id =  $unit->unit_id;
										$new_unit_report->student_id =  $user->id;
										$new_unit_report->capability_progress =  100;
										
										$user2 = \common\models\User::find()->select(['id'])->where(['email'=>$unit_key->attended_user])->one();										
										$new_unit_report->cap_done_by =  $user2->id;
									
										$new_unit_report->save();
								}
							
							}
							
						}
					
					} 
					
				}
				
			} 
	 	}
		
		echo "Capability Answer Completed"; 
	
	}	
	
	
	public function actionFetchUserTest()
    {

		$url = 'http://mycaar.com.au/wp-content/data_extract.php?type=user';
		$users = $this->fetch($url);
		$new_user = "";
		foreach( $users  as $user  )
		{	
			if($user->email == "peter@proweb.services")
				continue;
			
			$new_user = \common\models\User::find()->select('id')->where(['email'=>$user->email])->one()->id;
		
			if($new_user)
			{ 			
			 
				
			if(!empty($user->division))
			{				
				$divisiontest = \common\models\Division::find()->where(['title'=>$user->division,'company_id'=>1])->one();
				if($divisiontest)
					$division = $divisiontest->division_id;
				else
					$division = "";
			}
			else 
				$division = "";	
			
			
			if(!empty($user->role))	
			{
				$roletest = \common\models\Role::find()->where(['title'=>$user->role,'company_id'=>1])->one();
				if($roletest)
					$role = $roletest->role_id;
				else
					$role = "";
			}
			else 
				$role = "";	
			
			if(!empty($user->state))
			{				
				$statetest = \common\models\State::find()->where(['name'=>$user->state,'company_id'=>1])->one();
				
				if($statetest)
					$state = $statetest->state_id;
				else
					$state = "";
			}
			else
				$state = "";	
			
			if(!empty($user->location))	
			{
				$locationtest = \common\models\Location::find()->where(['name'=>$user->location,'company_id'=>1])->one();
				
				if($locationtest)
					$location = $locationtest->location_id;
				else
					$location = "";
			}
			else
				$location = "";
			
			$new_userprofile = \common\models\UserProfile::find()->where(['user_id'=>$new_user])->one();
			//$new_userprofile = new \common\models\UserProfile();
			$new_userprofile->user_id = $new_user;
			$new_userprofile->firstname = $user->firstname;
			$new_userprofile->lastname = $user->lastname; 
			$new_userprofile->division = $division; 
			$new_userprofile->role = $role; 
			$new_userprofile->state = $state; 
			$new_userprofile->location = $location; 
			$new_userprofile->save();
				
			}
			
unset($new_user);		
		} 
		echo "<pre>";
		
    }
	
	
}

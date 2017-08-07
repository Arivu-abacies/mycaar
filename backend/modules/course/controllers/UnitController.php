<?php

namespace backend\modules\course\controllers;

use Yii;
use common\models\Module;
use common\models\AwarenessQuestion;
use common\models\CapabilityQuestion;
use common\models\AwarenessOption;
use common\models\Unit;
use common\models\UnitElement;
use common\models\search\SearchUnit;
use common\models\ResetSchedule;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Embed\Embed;
/**
 * UnitController implements the CRUD actions for Unit model.
 */
class UnitController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
             'access' => [
                'class' => AccessControl::className(),
				'only' => ['create','delete'],
                'rules' => [
                    [
                        'actions' => ['create','delete'],
                        'allow' => true,
						'roles' => ['company_admin']
                    ],
                ],
            ], 
        ];
    }

    /**
     * Lists all Unit models.
     * @return mixed
     */
/*     public function actionIndex()
    {
        $searchModel = new SearchUnit();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    } */

    /**
     * Displays a single Unit model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
		$program = $model->module->program;
		if (\Yii::$app->user->can('manageProgram', ['post' => $program])) {
			return $this->render('view', [
				'model' => $this->findModel($id),
			]);			
		}else
		{
			//yii\web\ForbiddenHttpException
			throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
		}

    }

    /**
     * Creates a new Unit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($m_id)
    {
		//find model and see if this belongs to the user, else prohobit
		
        $model = new Unit();
		$module = Module::find()->where(['module_id'=>$m_id])->one();
		if(!$module){
			throw new NotFoundHttpException('The requested page does not exist.');
		}
		$program = $module->program;
		if (\Yii::$app->user->can('manageProgram', ['post' => $program])) {
        if(isset(Yii::$app->request->post()['unit_title'])) {
			$model->module_id = $m_id;
			$model->title = Yii::$app->request->post()['unit_title'];
			$model->status = Yii::$app->request->post()['unit_status'];
			if(Yii::$app->request->post()['reset_period'] != 0)
				$model->auto_reset_period = Yii::$app->request->post()['reset_period'];	
			else $model->auto_reset_period = NULL;
			$model->show_learning_page = Yii::$app->request->post()['show_learning_page'];	
			$previous_unit = Unit::find()->where(["module_id"=>$m_id])->orderBy('unit_order DESC')->one();
			if($previous_unit)
				$model->unit_order = $previous_unit->unit_order+1;
			if($model->save()){
				//save schedule here
				$this->saveAutoReset($model->unit_id,$model->auto_reset_period);
				//save elements as well
				$element = new UnitElement();
				$element->unit_id = $model->unit_id;
				$element->element_type = "page";
				$element->element_order = 1;
				$element->content = Yii::$app->request->post()['builder_data'];
				$element->save();
				//aw_dat
				$element = new UnitElement();
				$element->unit_id = $model->unit_id;
				$element->element_type = "aw_data";
				$element->element_order = 1;
				$element->content ='';
				$element->save();
				//cp_dat
				$element = new UnitElement();
				$element->unit_id = $model->unit_id;
				$element->element_type = "cap_data";
				$element->element_order = 1;
				$element->content ='';
				$element->save();
			}			
            return $this->redirect(['update', 'id' => $model->unit_id]);
        } else {
            return $this->render('add_unit', [
                'model' => $model,
				'module' => $module,
            ]);
        }
		}else
		{
			//yii\web\ForbiddenHttpException
			throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
		}
    }
	public function removeAutoReset($unit_id){
		$schedule = ResetSchedule::find()->where(['unit_id'=>$unit_id])->one();
		if($schedule){
			$old_command = $schedule->cron_time.' cd /home/wordpressmonks/public_html/works/mycaar_lms && php yii reset/unit '.$unit_id.PHP_EOL;
			if(file_exists('/tmp/crontab.txt')){
				//write cron_tab
				$output = shell_exec('crontab -l');
				 if($old_command){
					//removing
					 $output = str_replace($old_command, "", $output);
					 file_put_contents('/tmp/crontab.txt', $output); 
				} 
				//file_put_contents('/tmp/crontab.txt', $output.$new_cron_command); 
				exec('crontab /tmp/crontab.txt');					
				//print for debugging
				//$output = shell_exec('crontab -l');					
			}
			$schedule->delete();
		}
		return true;

	}
	/**
	 * Auto-reset lesson after a particular time period
	 */
	public function saveAutoReset($unit_id,$months){	
		if(Unit::findOne($unit_id) != null){
			if($months == NULL || $months == '' || $months == 0){
				$this->removeAutoReset($unit_id);	
				return true;
			}
			$today = time();
			$monthsLater = strtotime("+{$months} months", $today);
			$month = (int)date('m', $monthsLater);
			$date = (int)date('d', $monthsLater);
			
			//create the cron time
			//minute hour day month weekday
			$cron_time = "0 1 $date $month *";
			//$cron_time = "* * * * *"; //for debugging
			$new_cron_command = $cron_time.' cd /home/wordpressmonks/public_html/works/mycaar_lms && php yii reset/unit '.$unit_id.PHP_EOL;
			$old_command = false;
			
			//save schedule
			$schedule = ResetSchedule::find()->where(['unit_id'=>$unit_id])->one();
			if(!$schedule){
				$schedule = new ResetSchedule();
			}else{
				$old_command = $schedule->cron_time.' cd /home/wordpressmonks/public_html/works/mycaar_lms && php yii reset/unit '.$unit_id.PHP_EOL;
			}
			$schedule->unit_id = $unit_id;
			$schedule->cron_time = $cron_time;
			$schedule->actual_time = $monthsLater;
			$schedule->updated_at = date('Y-m-d H:i:s');
			if($schedule->save()){
				if(file_exists('/tmp/crontab.txt')){
					//write cron_tab
					$output = shell_exec('crontab -l');
					 if($old_command){
						//removing
						 $output = str_replace($old_command, "", $output);
						 file_put_contents('/tmp/crontab.txt', $output); 
					} 
					file_put_contents('/tmp/crontab.txt', $output.$new_cron_command); 
					exec('crontab /tmp/crontab.txt');					
					//print for debugging
					//$output = shell_exec('crontab -l');					
				}

				return true;
			}
		}

	}
    /**
     * Updates an existing Unit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$module = Module::find()->where(['module_id'=>$model->module_id])->one();
		$program = $module->program;
		$current_reset_period = $model->auto_reset_period;
		if (\Yii::$app->user->can('manageProgram', ['post' => $program])) {
			if(isset(Yii::$app->request->post()['unit_title'])) {
				$model->title = Yii::$app->request->post()['unit_title'];
				$model->status = Yii::$app->request->post()['unit_status'];
				if(Yii::$app->request->post()['reset_period'] != 0)
					$model->auto_reset_period = Yii::$app->request->post()['reset_period'];
				else $model->auto_reset_period = NULL;
				$model->show_learning_page = Yii::$app->request->post()['show_learning_page'];	
				if($model->save()){
					//if any change in the auto_reset_period, then only alter the crontab
					if($current_reset_period != $model->auto_reset_period)
						$this->saveAutoReset($model->unit_id,$model->auto_reset_period);
					//save elements as well
					$element = UnitElement::find()->where(['unit_id'=>$id,'element_type'=>'page'])->one();
					$element->unit_id = $model->unit_id;
					$element->element_type = "page";
					$element->element_order = 1;
					$element->content = Yii::$app->request->post()['builder_data'];
					$element->save();
				}
				return $this->redirect(['update', 'id' => $model->unit_id]);
			} else {
				return $this->render('update_unit', [
					'model' => $model,
					'module' => $module,
				]);
			}
		}else
		{
			//yii\web\ForbiddenHttpException
			throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
		}
    }

    /**
     * Deletes an existing Unit model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
		return $this->redirect(['program/program-list']);
        //return $this->redirect(['index']);
    }

    /**
     * Finds the Unit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Unit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Unit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	/**
	 * Save the media to the uploads folder
	 * Return the path
	 */
	public function actionUpload(){
		//$name = uniqid();
		$name = preg_replace( 
                     array("/\s+/", "/[^-\.\w]+/"), 
                     array("_", ""), 
                     trim($_FILES["media"]["name"])); 
		$dir = "uploads/media/";
		move_uploaded_file($_FILES["media"]["tmp_name"], $dir. $name);
		return \Yii::$app->homeUrl.$dir. $name;
	}
	
	/**
	 * Save the tests actionCreate
	 * Parms: type- aw or cp 
	 * Reload the update page after save
	 */
	public function actionSaveTest($type){	

		$output = [];
		$data = json_decode(Yii::$app->request->post()['data']);	
		$questions = $this->formatQuestions($data->html);
		
		if($type == "aw")
			$this->saveQuestions(Yii::$app->request->post()['unit_id'],$questions);
		if($type == "cp")
			$this->saveCapQuestions(Yii::$app->request->post()['unit_id'],$questions);
		return $this->redirect(['update', 'id' => Yii::$app->request->post()['unit_id']]);
	}
	
	/**
	 * Format questios array from string to array
	 * Parms: $data - formdata returned by formbuilder
	 * Returns array
	 */	
	public function formatQuestions($data){
		//print_r($data);die;
		$output = [];
		$html = str_replace("<fields>","",$data);
		$html = str_replace("</fields>","",$html);
		$questions = [];
		preg_match_all('/<field[^>]*?>([\s\S]*?)<\/field>/', $html,$questions, PREG_SET_ORDER);
		foreach($questions as $question){
			$field_reg = $question[0];
			$options_reg = $question[1];
			$data = [
				'question' => [],
				'type' => [],
				'options' => [],				
				'id' => [],
				'answer' => [],
				'description' => [],
				'src' => []
			];
	
	 	preg_match_all('/<option[^>]*?>([\s\S]*?)<\/option>/', $options_reg,$data['options'], PREG_SET_ORDER); 
	 	
	 	// by ARIVU For Using [ > ] Greater symbol in Option Value 
		foreach($data['options'] as $key=>$dat ) {
			$str2 = strstr($dat[1], 'option_id');
			if($str2)
			{
			$str3 = strstr($str2, '>');			
			$checkvalue = substr($str3, 1);  
			$data['options'][$key][1] = $checkvalue;
			}			
		}
		
		  foreach($data['options'] as $key=>$dat){
			preg_match_all('/option_id="([\s\S]*?)"/', $dat[0],$data['options'][$key][2], PREG_SET_ORDER);
		}  
		preg_match_all('/type="([\s\S]*?)"/', $field_reg,$data['type'], PREG_SET_ORDER);
		preg_match_all('/description="([\s\S]*?)"/', $field_reg,$data['description'], PREG_SET_ORDER);
		preg_match_all('/[^<option] label="([\s\S]*?)"/', $field_reg,$data['question'], PREG_SET_ORDER);
		preg_match_all('/name="([\s\S]*?)"/', $field_reg,$data['id'], PREG_SET_ORDER);
		preg_match_all('/selected="true">([\s\S]*?)</', $options_reg,$data['answer'], PREG_SET_ORDER);
		preg_match_all('/src="([\s\S]*?)"/', $field_reg,$data['src'], PREG_SET_ORDER);
		$output[] = $data;
	
		}
		//print_r($output);die;
		return $output;		
	}
	
	/**
	 * Save awareness questions and options
	 * Parms: unit_id , questions array 
	 * Return null
	 */	
	public function saveQuestions($unit_id,$questions){
		//see if any questions deleted
		$from_update = $to_update = [];
		$current_qstns = Unit::findOne($unit_id)->awarenessQuestions;
		foreach($current_qstns as $q){
			$from_update[] = $q->aq_id;
		}
		$html  = '<form-template><fields>';			
		foreach($questions as $order => $quest){
			
			$id = $quest['id'][0][1];
			$id = preg_replace("/[^0-9]/","",$id);//die;
			$awareness_question = AwarenessQuestion::find()->where(['aq_id'=>$id,'unit_id'=>$unit_id])->one();			
			if(!$awareness_question){
				$awareness_question = new AwarenessQuestion();
				$awareness_question->unit_id = $unit_id;
			}else{
				//get the existing questions list
				$to_update[] = $id;
			}
			$awareness_question->question = $quest['question'][0][1];
			$awareness_question->question_type = $quest['type'][0][1];
			$awareness_question->src = $quest['src'][0][1];
			$awareness_question->order_id = $order;
			$answer = "";
			$description = $awareness_question->description  = '';
			if(isset($quest['description'][0][1]))
					$awareness_question->description  = $quest['description'][0][1];
			if($awareness_question->save(false)){
				//reformat the form data
				$name = $quest['type'][0][1]."-".$awareness_question->aq_id; //change this to primary key
				$type = $class = $quest['type'][0][1];
				
				//echo $label = htmlentities($quest['question'][0][1]), '.....';
				
				$label = htmlentities($quest['question'][0][1], ENT_QUOTES, 'UTF-8'); 
				//$label = $quest['question'][0][1]; 
				$src = $quest['src'][0][1];
				if(isset($quest['description'][0][1]))
					$description = htmlentities($quest['description'][0][1], ENT_QUOTES, 'UTF-8');
					//$description = $quest['description'][0][1];
				$html .= "<field type='$type' description='$description' label='$label' class='$class' name='$name' src='$src'>";					
				//////////////
				if(!empty($quest['options'])){
					$from_update_options = $to_update_options = [];
					$current_optns = $awareness_question->awarenessOptions;
					foreach($current_optns as $op){
						$from_update_options[] = $op->option_id;
					}
					foreach($quest['options'] as $opt){
						$optn = htmlentities($opt[1], ENT_QUOTES, 'UTF-8');
						$option_id = $opt[2][0][1];
						$awareness_option = AwarenessOption::findOne($option_id);
						if (!$awareness_option) 
							$awareness_option = new AwarenessOption();
						else
							$to_update_options[] = $awareness_option->option_id;

						$awareness_option->question_id =  $awareness_question->aq_id;
						$awareness_option->answer = $opt[1];
						$awareness_option->save();
						$opt_string = "<option option_id='{$awareness_option->option_id}'  label='$optn ' value='$optn '>'$optn '</option>";
							if(!empty($quest['answer'])){
								foreach($quest['answer'] as $ans){
									if($ans[1] == $opt[1]){
										$answer .= $awareness_option->option_id."_";
										$opt_string = "<option option_id='{$awareness_option->option_id}' label='$optn ' value='$optn ' selected='true'>'$optn'</option>";
									}
									 
								}
							}
						$html .= $opt_string;
					}
					$deleted=array_diff($from_update_options,$to_update_options);
						foreach($deleted as $del){
							AwarenessOption::findOne($del)->delete();
					}
				}
				$html .= '</field>';
				$answer = trim($answer, "_");
				$awareness_question->answer = $answer;
				$awareness_question->save();
				//return true;
				
			}
		}
		$html  .= '</fields></form-template>';
		$element = UnitElement::find()->where(['unit_id'=>$unit_id,'element_type'=>'aw_data'])->one();
		if(!$element)
			$element = new UnitElement();
		
		//echo $html;exit;
		$element->unit_id = $unit_id;
		$element->element_type = "aw_data";
		$element->element_order = 1;
		$element->content = $html;
		$element->save();
		//delete the elements
		$deleted=array_diff($from_update,$to_update);
		foreach($deleted as $del){
			AwarenessQuestion::findOne($del)->delete();
		}
	}	
	
	/**
	 * Save capability questions
	 * No options are saved here
	 * Parms: unit_id , questions array 
	 * Return null
	 */	
	public function saveCapQuestions($unit_id,$questions){
		//see if any questions deleted
		$from_update = $to_update = [];
		$current_qstns = Unit::findOne($unit_id)->capabilityQuestions;
		foreach($current_qstns as $q){
			$from_update[] = $q->cq_id;
		}
		$html  = '<form-template><fields>';			
		foreach($questions as $order => $quest){		
			$id = $quest['id'][0][1];
			$id = preg_replace("/[^0-9]/","",$id);//die;
			$cap_question = CapabilityQuestion::find()->where(['cq_id'=>$id,'unit_id'=>$unit_id])->one();			
			if(!$cap_question){
				$cap_question = new CapabilityQuestion();
				$cap_question->unit_id = $unit_id;
			}else{
				//get the existing questions list
				$to_update[] = $id;
			}
			$cap_question->question = $quest['question'][0][1];
			//$cap_question->question_type = $quest['type'][0][1];
			$cap_question->order_id = $order;
			$answer = "";
			$description = $cap_question->description  = '';
			if(isset($quest['description'][0][1]))
					$cap_question->description  = $quest['description'][0][1];
			if($cap_question->save(false)){
				//reformat the form data
				$name = $quest['type'][0][1]."-".$cap_question->cq_id; //change this to primary key
				$type = $class = $quest['type'][0][1];
				$label = htmlentities($quest['question'][0][1], ENT_QUOTES, 'UTF-8');
				if(isset($quest['description'][0][1]))
					$description = htmlentities($quest['description'][0][1], ENT_QUOTES, 'UTF-8');
				$html .= "<field type='$type' description='$description' label='$label' class='$class' name='$name' src='false'>";					
				//////////////
				if(!empty($quest['options'])){
					foreach($quest['options'] as $opt){
						$optn = htmlentities($opt[1], ENT_QUOTES, 'UTF-8');
						$opt_string = "<option  label='$optn' value='$optn'>'$optn'</option>";
							if(!empty($quest['answer'])){
								foreach($quest['answer'] as $ans){
									if($ans[1] === $opt[1]){
										$answer = $optn;
										//$optn = htmlspecialchars($opt[1]);
										$opt_string = "<option label='{$optn}' value='$optn' selected='true'>'$optn'</option>";
									}
									 
								}
							}
						$html .= $opt_string;
					}
				}
				$html .= '</field>';
				$cap_question->answer = $answer;
				$cap_question->save();
				//return true;
				
			}
		}
		$html  .= '</fields></form-template>';
		$element = UnitElement::find()->where(['unit_id'=>$unit_id,'element_type'=>'cap_data'])->one();
		if(!$element)
			$element = new UnitElement();
		
		$element->unit_id = $unit_id;
		$element->element_type = "cap_data";
		$element->element_order = 1;
		$element->content = $html;
		$element->save();
		//delete the elements
		$deleted=array_diff($from_update,$to_update);
		foreach($deleted as $del){
			CapabilityQuestion::findOne($del)->delete();
		}	
		//if deleting all cap questions, reset users who had attended the cap
		$reports = \common\models\UnitReport::find()->where(['unit_id'=>$unit_id])->all();
		foreach($reports as $report){
			$report->capability_progress = NULL;
			$report->save();
		}
		
	}
	public function actionReOrder(){
		$data = \Yii::$app->request->post()['data'];
		//print_r($data);die;
		foreach($data as $order=>$unit){
			$unit = $this->findModel($unit['id']);
			$unit->unit_order = $order;
			$unit->save();
		}
		return true;
	}
	public function actionSort(){
		$data = \Yii::$app->request->post();
		//print_r($data);die;
		foreach($data['unit'] as $order=>$unit){
			//echo $unit;
			$unit = $this->findModel($unit);
			$unit->unit_order = $order;
			$unit->save();
		}
		return true;
	}
	
	public function actionPreview($u_id){
        $model = $this->findModel($u_id);
		$program = $model->module->program;
		if (\Yii::$app->user->can('manageProgram', ['post' => $program])) {
			$questions = AwarenessQuestion::find()->where('unit_id = :unit_id', [':unit_id' => $u_id])->orderBy('order_id ASC')->orderBy('order_id ASC')->all();
			foreach($questions as $key=>$quest){
				$questions[$key]['options'] = $quest->awarenessOptions;
			}
		$cp_questions = CapabilityQuestion::find()->where('unit_id = :unit_id', [':unit_id' => $u_id])->orderBy('order_id ASC')->all();
			return $this->render('preview', [
				'model' => $this->findModel($u_id),
				'questions' => $questions,
				'cp_questions' => $cp_questions
			]);			
		}else
		{
			//yii\web\ForbiddenHttpException
			throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
		}
	}
	
	public function actionEmbed($url){
		
		//Load any url:
		$info = Embed::create($url);				
		//var_dump($info->code);die;
		//echo preg_match_all('/src="([\s\S]*?)"/', $info->code,$src[], PREG_SET_ORDER);die;
		if($info && $info->code){			
			$xpath = new \DOMXPath(@\DOMDocument::loadHTML($info->code));
			$src = $xpath->evaluate("string(//iframe/@src)");	
			if(!$src)
				return $url;			
		}
		if (preg_match('/youtube.com/',$info->code))
			return $src."&rel=0";
		else
			return $src;
	}
}

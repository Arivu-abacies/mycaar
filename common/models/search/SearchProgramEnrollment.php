<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\models\Location;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use yii\db\Query;
/**
 * SearchProgramEnrollment represents the model behind the search form about `common\models\ProgramEnrollment`.
 */
class SearchProgramEnrollment extends User
{
    /**
     * @inheritdoc
     */
	 

	public $division;
	public $location;
	public $role;
	public $state;
	public $Program;
	public $username;
	public $fullname;
	public $enrollcheck;
	public $accesslevel;
	
    public function rules()
    {
        return [
            [['id'], 'integer'],

			[['location','role','state','division','Program','username','fullname','enrollcheck','accesslevel'], 'safe'],	
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
		/// Variable for Temporary Following Condition.
		$program_id = '';
		$username = '';
		$enroll = '';
		$fullname = '';
		
		/// Search Program id With Query
		if(isset($params['program_id']) && !empty($params['program_id']))
			$program_id = $params['program_id'];
		
		/// Search Username or Email With Query
		if(isset($params['SearchProgramEnrollment']['username']) && !empty($params['SearchProgramEnrollment']['username']))
			$username = $params['SearchProgramEnrollment']['username'];
		
		/// Search Enrolled Wise and UnEnrolled Wise With Query && Combination With Fullname Temporary Column
		
		if(isset($params['SearchProgramEnrollment']['enrollcheck']) && ($params['SearchProgramEnrollment']['enrollcheck'] != ''))
			$enroll = "where tmp.is_enrolled = ".$params['SearchProgramEnrollment']['enrollcheck'];
		
		/// Search Fullname ( Firstname and Lastname ) Wise With Query && Combination With Enrolled Wise Temporary Column
		if(isset($params['SearchProgramEnrollment']['fullname']) && !empty($params['SearchProgramEnrollment']['fullname']))
		{
		  if(isset($params['SearchProgramEnrollment']['enrollcheck']) && ($params['SearchProgramEnrollment']['enrollcheck'] != ''))
		  {
			  /// Both Enrolled Wise and Fullname wise search it will Printed in Query
			$fullname = ' and tmp.fullname LIKE "%'.$params['SearchProgramEnrollment']['fullname'].'%"';		 
		  }else{
			  /// Fullname search only not Enrolled it will Printed in Query
			$fullname = 'where tmp.fullname LIKE "%'.$params['SearchProgramEnrollment']['fullname'].'%"';	
		  }
		}
			// Sub Query Temporary table where Condition
			// Depends Upon both Temporary where Enrolled and Fullname Where Condtion.
			$tmpwhere = $enroll.$fullname;
		
		// SQL Query  Temporary column  is_enrolled in Select FieldCondition
		
		$dataProvider = new SqlDataProvider([
		'sql' => 'select * from ( select 
						`p`.`program_id` AS `program_id`,
						`u`.`id` AS `id`,
						`u`.`username` AS `username`,
						CONCAT(`up`.firstname," ",`up`.lastname) AS `fullname`,
						`u`.`c_id` AS `c_id`,
						if((`pe`.`e_id` is not null),1,0) AS `is_enrolled` 
					from 
						((`program` `p` 
					join 
						`user` `u` FORCE INDEX (`c_id`) on((`u`.`c_id` = `p`.`company_id` and u.`c_id` = '.Yii::$app->user->identity->c_id.' and p.program_id = '.$program_id.' and  (u.username like "%'.$username.'%")
						))) 
					left join 
						`user_profile` `up` on ((`up`.`user_id` = `u`.`id`))	
					left join 
						`program_enrollment` `pe` on(((`pe`.`program_id` = `p`.`program_id`) and (`pe`.`user_id` = `u`.`id`)
						))) 
						
						) as tmp '.$tmpwhere.' order by tmp.fullname' ,
	

			'pagination' => [
				'pageSize' => 0,
				],
			]);
			
			
		if (!($this->load($params) && $this->validate())) {		
			return $dataProvider;
		}
		
        return $dataProvider;
    }
	
	  public function searchfilter($params,$extrasearch)
    {
		
	$selected_company = \Yii::$app->user->identity->c_id;

	if(Yii::$app->user->can("company_assessor")){
			$getlocation = Location::find()->where(['company_id'=>$selected_company])->orderBy('name')->all();
		    foreach($getlocation as $key=>$get)
			{
			 $location[$key]= $get->location_id;		
		    }	
		}
	else if(Yii::$app->user->can("group_assessor")){
		$access_location = \Yii::$app->user->identity->userProfile->access_location;
		if(!empty($access_location))
		 $useraccesslocation = explode(",",$access_location);
	 
		$getlocation = Location::find()->where(['company_id'=>$selected_company])->orderBy('name')->all();
		foreach($getlocation as $key=>$get)
		{
			if(isset($useraccesslocation) && in_array($get->location_id,$useraccesslocation))
			{
			 $location[$key]= $get->location_id;
			}
		}	
	}
	else if(Yii::$app->user->can("local_assessor")){
		$locationid = \Yii::$app->user->identity->userProfile->location;
		$location = Location::find()->where(['company_id'=>$selected_company,'location_id'=>$locationid])->orderBy('name')->all();
	}

		
		$setlocation = implode(",",$location);
		
		
		/// Variable for Temporary Following Condition.
		$program_id = '';
		$division = '';
		$role = '';
		$location = '';
		$state = '';
		$accesslevel = '';		
		$username = '';
		$enrollcheck = '';
		$fullname = '';
		$wherecondition = '';
		$tmpwhere = '';
		
		
		
		$params['SearchProgramEnrollment']['Program'] = $extrasearch['Program'];		
		$params['SearchProgramEnrollment']['accesslevel'] = $extrasearch['accesslevel'];		
		$params['SearchProgramEnrollment']['enrollcheck'] = $extrasearch['enrollcheck'];		
		$params['SearchProgramEnrollment']['fullname'] = $extrasearch['fullname'];		
		$params['SearchProgramEnrollment']['username'] = $extrasearch['username'];		
		$params['SearchProgramEnrollment']['division'] = $extrasearch['division'];
	 	$params['SearchProgramEnrollment']['role'] = $extrasearch['role'];
		$params['SearchProgramEnrollment']['location'] = $extrasearch['location'];
		$params['SearchProgramEnrollment']['state'] = $extrasearch['state']; 
		
		/// Search Program id With Query
		if(isset($params['SearchProgramEnrollment']['Program']) && !empty($params['SearchProgramEnrollment']['Program']))
			$program_id = $params['SearchProgramEnrollment']['Program'];
		
		/// Search Access level With Query
		if(isset($params['SearchProgramEnrollment']['accesslevel']) && !empty($params['SearchProgramEnrollment']['accesslevel']))
			$accesslevel = " and tmp.`accesslevel` = '".$params['SearchProgramEnrollment']['accesslevel']."'";
		
		/// Search Division id With Query
		if(isset($params['SearchProgramEnrollment']['division']) && !empty($params['SearchProgramEnrollment']['division']))
			$division = " and tmp.`division` =".$params['SearchProgramEnrollment']['division'];
		
		 /// Search Role id With Query
		if(isset($params['SearchProgramEnrollment']['role']) && !empty($params['SearchProgramEnrollment']['role']))
			$role = " and tmp.`role` =".$params['SearchProgramEnrollment']['role'];
		
		/// Search Location id With Query
		if(isset($params['SearchProgramEnrollment']['location']) && !empty($params['SearchProgramEnrollment']['location']))
			$location = " and tmp.`location` =".$params['SearchProgramEnrollment']['location'];
		
		/// Search State id With Query
		if(isset($params['SearchProgramEnrollment']['state']) && !empty($params['SearchProgramEnrollment']['state']))
			$state = " and tmp.`state` =".$params['SearchProgramEnrollment']['state']; 
		
		
		/// Search Username or Email With Query
		if(isset($params['SearchProgramEnrollment']['username']) && !empty($params['SearchProgramEnrollment']['username']))
			$username = $params['SearchProgramEnrollment']['username'];
		
		/// Search Enrolled Wise and UnEnrolled Wise With Query && Combination With Fullname Temporary Column
		
		if(isset($params['SearchProgramEnrollment']['enrollcheck']) && ($params['SearchProgramEnrollment']['enrollcheck'] != ''))
			$enrollcheck = " and tmp.is_enrolled = ".$params['SearchProgramEnrollment']['enrollcheck'];
		
		/// Search Fullname ( Firstname and Lastname ) Wise With Query && Combination With Enrolled Wise Temporary Column
		if(isset($params['SearchProgramEnrollment']['fullname']) && !empty($params['SearchProgramEnrollment']['fullname']))
		{
		 
			 /// Both Enrolled Wise and Fullname wise search it will Printed in Query
			$fullname = ' and tmp.fullname LIKE "%'.$params['SearchProgramEnrollment']['fullname'].'%"';		 
		  
		}
			// Sub Query Temporary table where Condition
			// Depends Upon both Temporary where Enrolled and Fullname Where Condtion.
			$wherecondition = $enrollcheck.$fullname.$accesslevel.$division.$role.$state.$location;
			
			if($wherecondition)
			{	$str2 = substr($wherecondition, 4);
				$tmpwhere = " where ".$str2." ";
			}
		// SQL Query  Temporary column  is_enrolled in Select FieldCondition
		
		$dataProvider = new SqlDataProvider([
		'sql' => 'select * from ( select 
						`p`.`program_id` AS `program_id`,
						`u`.`id` AS `id`,
						`u`.`username` AS `username`,
						CONCAT(`up`.firstname," ",`up`.lastname) AS `fullname`,
						`up`.division AS `division`,
						`up`.`role` AS `role`,
						`up`.`location` AS `location`,
						`up`.`state` AS `state`,
						`u`.`c_id` AS `c_id`,
						`au`.`item_name` AS `accesslevel`,
						if((`pe`.`e_id` is not null),1,0) AS `is_enrolled` 
					from 
						((`program` `p` 
					join 
						`user` `u` FORCE INDEX (`c_id`) on((`u`.`c_id` = `p`.`company_id` and u.`c_id` = '.Yii::$app->user->identity->c_id.' and p.program_id = '.$program_id.' and  (u.username like "%'.$username.'%")
						))) 
					left join 
						`auth_assignment` `au` on ((`au`.`user_id` = `u`.`id`)) 	
					left join 
						`user_profile` `up` on ((`up`.`user_id` = `u`.`id`)) 
					left join 
						`program_enrollment` `pe` on(((`pe`.`program_id` = `p`.`program_id`) and (`pe`.`user_id` = `u`.`id`)
						))) where up.location in ('.$setlocation.') 
						
						) as tmp '.$tmpwhere.'order by tmp.fullname' ,
	

			'pagination' => [
				'pageSize' => 0,
				],
			]);
			  
		if (!($this->load($params) && $this->validate())) {		
			return $dataProvider;
		}
		
        return $dataProvider;
    }
}

<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UnitReport;

/**
 * SearchUserProfile represents the model behind the search form about `common\models\UserProfile`.
 */
class SearchUnitReport extends UnitReport
{
    public $module_id;
	
	public $division;
	public $location;
	public $role;
	public $state;
	public $accesslevel;
	public $programid;
	public $moduleid;
	public $unitid;
	
	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          //  [['unit_id'], 'integer'],
            [['cap_done_by','student_id','unit_id','module_id'], 'string'],
			 [['location','role','state','division','accesslevel','updated_at','unitid','moduleid','programid'], 'safe'],

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
    public function search($params,$extrasearch)
    {

		if(isset($extrasearch['accesslevel']) && $extrasearch['accesslevel']!='')
			$params['SearchUnitReport']['accesslevel'] = $extrasearch['accesslevel'];
		if(isset($extrasearch['division']) && $extrasearch['division']!='')
			$params['SearchUnitReport']['division'] = $extrasearch['division'];
		if(isset($extrasearch['role']) && $extrasearch['role']!='')
			$params['SearchUnitReport']['role'] = $extrasearch['role'];
		if(isset($extrasearch['location']) && $extrasearch['location']!='')
			$params['SearchUnitReport']['location'] = $extrasearch['location'];
		if(isset($extrasearch['state']) && $extrasearch['state']!='')
			$params['SearchUnitReport']['state'] = $extrasearch['state']; 
		if(isset($extrasearch['programid']) && $extrasearch['programid']!='')
			$params['SearchUnitReport']['programid'] = $extrasearch['programid'];
		if(isset($extrasearch['moduleid']) && $extrasearch['moduleid']!='')
			$params['SearchUnitReport']['moduleid'] = $extrasearch['moduleid'];
		if(isset($extrasearch['unitid']) && $extrasearch['unitid']!='')
			$params['SearchUnitReport']['unitid'] = $extrasearch['unitid'];

        $query = UnitReport::find()->where(['not',['cap_done_by'=>NULL]]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,

        ]);
		$query->joinWith(['unit as u']);
		$query->joinWith(['unit.module as m']);
		$query->joinWith(['user_profile as profile']);
		$query->joinWith(['assessor as assr']);
		$query->joinWith(['assessor.user']);
		$query->joinWith(['authRole as rolelist']);	
		//$query->joinWith(['program']);
		$query->andFilterWhere(['user.c_id'=>\Yii::$app->user->identity->c_id,'user.status'=>10]);
		//$query->andFilterWhere(['u.status'=>1]);
		//$query->andFilterWhere(['m.status'=>1]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
		$query->andFilterWhere(['or',
						['like', 'assr.firstname', $this->cap_done_by],
						['like','assr.lastname', $this->cap_done_by]]);
		$query->andFilterWhere(['or',
						['like', 'profile.firstname', $this->student_id],
						['like','profile.lastname', $this->student_id]]);
		$query->andFilterWhere(['like', 'u.title', $this->unit_id]);
		$query->andFilterWhere(['like', 'm.title', $this->module_id]);
		$query->andFilterWhere(['=', 'm.program_id', $this->programid]);
		$query->andFilterWhere(['=', 'm.module_id', $this->moduleid]);
		$query->andFilterWhere(['=', 'u.unit_id', $this->unitid]);
		
		$query->andFilterWhere(['=', 'profile.division', $this->division]);        
        $query->andFilterWhere(['=', 'profile.location', $this->location]);        
        $query->andFilterWhere(['=', 'profile.role', $this->role]);        
        $query->andFilterWhere(['=', 'profile.state', $this->state]);
		$query->andFilterWhere(['like', 'rolelist.item_name', $this->accesslevel]);   		
		$query->andFilterWhere(['like', 'unit_report.updated_at', $this->updated_at]);  	
		
		if(!Yii::$app->user->can("superadmin")){   
			 if(Yii::$app->user->can("group_assessor")){		
				$setlocation = \Yii::$app->user->identity->userProfile->access_location;			  
				$query->andFilterWhere(['in', 'profile.location', $setlocation]);
			  }
			  else if(Yii::$app->user->can("local_assessor")){	
				$query->andFilterWhere(['profile.location'=>\Yii::$app->user->identity->userProfile->location]);
			  }    
		
		   }
		   
		//}

        return $dataProvider;
    }

	public function searchCustom($param)
	{
		 $query = UnitReport::find();
        // add conditions that should always apply here
		//print_R($param);die;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		$query->joinWith(['user_profile.user']);
		$query->joinWith(['unit as u']);
		$query->joinWith(['unit.module as m']);
		//$query->joinWith(['unit.module.program as p']);
		//$query->joinWith(['program']);
		$query->andFilterWhere(['user.c_id'=>\Yii::$app->user->identity->c_id,'user.status'=>10]);
		$query->andFilterWhere(['u.status'=>1]);
		$query->andFilterWhere(['m.status'=>1]);
/* 		if($type == "module")
			$query->andFilterWhere(['m.module_id'=>$unit_id]);
		if($type == "program")
			$query->andFilterWhere(['p.program_id'=>$unit_id]);	 */	
			if(isset($param['program']) && $param['program'] !='')
				$query->andFilterWhere(['m.program_id'=>$param['program']]);	
			if(isset($param['module']) && $param['module'] !='')
				$query->andFilterWhere(['m.module_id'=>$param['module']]);
			if(isset($param['unit']) && $param['unit'] !='')
				$query->andFilterWhere(['u.unit_id'=>$param['unit']]);			
			if(isset($param['user_id']) && $param['user_id'] !='')
				$query->andFilterWhere(['user_id'=>$param['user_id']]);					
 			if(isset($param['state']) && $param['state'] !='')
				$query->andFilterWhere(['state'=>$param['state']]);
			if(isset($param['role']) && $param['role'] !='')
				$query->andFilterWhere(['role'=>$param['role']]);
			if(isset($param['location']) && $param['location'] !='')
				$query->andFilterWhere(['location'=>$param['location']]);
			if(isset($param['division']) && $param['division'] !='')
				$query->andFilterWhere(['division'=>$param['division']]); 
			if(isset($param['firstname']) && $param['firstname'] !='')
				$query->andFilterWhere(['like', 'firstname',$param['firstname']]);
			if(isset($param['lastname']) && $param['lastname'] !='')
				$query->andFilterWhere(['like', 'lastname', $param['lastname']]);	
		return $dataProvider;
	}
}

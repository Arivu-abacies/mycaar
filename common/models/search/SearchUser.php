<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\models\UserProfile as Profile;


/**
 * SearchUser represents the model behind the search form about `common\models\User`.
 */
class SearchUser extends User
{
    /**
     * @inheritdoc
     */
	public $division;
	public $location;
	public $role;
	public $state;
	public $firstname;
	public $lastname;
	public $roleName;
	
	 
    public function rules()
    {
        return [
            [['id', 'role', 'c_id', 'status', 'created_at', 'updated_at',], 'integer'],
            [['location','role','state','division','username','firstname','lastname', 'roleName','auth_key', 'password_hash', 'password_reset_token', 'email',], 'safe'],					
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
		
		if(\Yii::$app->user->can('company manage')) 
		{
			 $query = User::find()->where(['status'=>10]);			 			 
		} 		
		else if((\Yii::$app->user->can('company_admin'))||(\Yii::$app->user->can('company_assessor'))||(\Yii::$app->user->can('group_assessor'))||(\Yii::$app->user->can('"local_assessor"')))
		{			
			 $query = User::find()->where(['c_id' =>Yii::$app->user->identity->c_id,'status'=>10]);
		}
        // add conditions that should always apply here
		
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            
        ]);
				
		$query->joinWith(['userProfile as user_profile']);		
		$query->joinWith(['authRole as rolelist']);	
		
		if(\Yii::$app->user->can('sysadmin')) 
		{			
		} 
		else if(\Yii::$app->user->can('superadmin'))
		{
			$query->andWhere(['<>','rolelist.item_name','sysadmin']);
		}
		else if((\Yii::$app->user->can('company_admin'))||(\Yii::$app->user->can('company_assessor'))||(\Yii::$app->user->can('group_assessor'))||(\Yii::$app->user->can('"local_assessor"')))
		{
			$query->andWhere(['not in','rolelist.item_name',['sysadmin','superadmin']]);
		}			
	
		
		$query->orderBy('user_profile.firstname ASC');
        $this->load($params);
	
         if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        } 

		
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'role' => $this->role,
            'c_id' => $this->c_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'user_profile.firstname', $this->firstname])
            ->andFilterWhere(['like', 'user_profile.lastname', $this->lastname])        
            ->andFilterWhere(['like', 'rolelist.item_name', $this->roleName]);        
                 
		
        return $dataProvider;
		
    }
	 
	
	  public function searchcompanyadmin($params,$extrasearch)
    {
		
		
		$params['SearchUser']['email'] = $extrasearch['email'];
		$params['SearchUser']['firstname'] = $extrasearch['firstname'];
		$params['SearchUser']['lastname'] = $extrasearch['lastname'];
		$params['SearchUser']['roleName'] = $extrasearch['roleName'];
		
		$params['SearchUser']['division'] = $extrasearch['division'];
	 	$params['SearchUser']['role'] = $extrasearch['role'];
		$params['SearchUser']['location'] = $extrasearch['location'];
		$params['SearchUser']['state'] = $extrasearch['state']; 
		
		if(\Yii::$app->user->can('company manage')) 
		{
			 $query = User::find()->where(['status'=>10]);			 			 
		} 		
		else if((\Yii::$app->user->can('company_admin'))||(\Yii::$app->user->can('company_assessor'))||(\Yii::$app->user->can('group_assessor'))||(\Yii::$app->user->can('"local_assessor"')))
		{			
			 $query = User::find()->where(['c_id' =>Yii::$app->user->identity->c_id,'status'=>10]);
		}
        // add conditions that should always apply here
		
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            
        ]);
				
		$query->joinWith(['userProfile as user_profile']);	
		$query->joinWith(['authRole as rolelist']);	
		
		if(\Yii::$app->user->can('sysadmin')) 
		{			
		} 
		else if(\Yii::$app->user->can('superadmin'))
		{
			$query->andWhere(['<>','rolelist.item_name','sysadmin']);
		}
		else if((\Yii::$app->user->can('company_admin')) ||(\Yii::$app->user->can('company_assessor'))||(\Yii::$app->user->can('group_assessor'))||(\Yii::$app->user->can('"local_assessor"')))
		{
			$query->andWhere(['not in','rolelist.item_name',['sysadmin','superadmin']]);
			
		}			
		
		
		$query->orderBy('user_profile.firstname ASC');
        $this->load($params);
	
         if (!$this->validate()) {
            return $dataProvider;
        } 

		
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'role' => $this->role,
            'c_id' => $this->c_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'user_profile.firstname', $this->firstname])
            ->andFilterWhere(['like', 'user_profile.lastname', $this->lastname])        
            ->andFilterWhere(['=', 'user_profile.division', $this->division])        
            ->andFilterWhere(['=', 'user_profile.location', $this->location])        
            ->andFilterWhere(['=', 'user_profile.role', $this->role])        
            ->andFilterWhere(['=', 'user_profile.state', $this->state])      
            ->andFilterWhere(['like', 'rolelist.item_name', $this->roleName]); 
			
           if(!Yii::$app->user->can("superadmin")){   
			 if(Yii::$app->user->can("group_assessor")){		
				$setlocation = \Yii::$app->user->identity->userProfile->access_location;			  
				$query->andFilterWhere(['in', 'location', $setlocation]);
			  }
			  else if(Yii::$app->user->can("local_assessor")){	
				$query->andFilterWhere(['location'=>\Yii::$app->user->identity->userProfile->location]);
			  }    
		
		   }
        return $dataProvider;
		
    }
}

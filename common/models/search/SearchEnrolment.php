<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Enrolment;
/* use common\models\ProgramEnrollment;
use yii\db\Query;
use yii\db\Command;
use yii\db\Connection; */

/**
 * SearchUser represents the model behind the search form about `common\models\Enrolment`.
 */
class SearchEnrolment extends Enrolment
{
    /**
     * @inheritdoc
     */
	 
    public function rules()
    {
        return [
               [['id','is_enrolled'], 'integer'],	
			   [['username'], 'string'],
			   [['username'], 'safe'], 				   
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
		
		//$query = Enrolment::getEnrolllist(3,1);	  
		
		   $query = Enrolment::find()->where(['c_id' =>Yii::$app->user->identity->c_id,'program_id'=>isset($params['program_id'])?$params['program_id']:'']);   
		
		/* $connection = Yii::$app->getDb(); 
		$command = $connection->createCommand('select 
	`p`.`program_id` AS `program_id`,
	`u`.`id` AS `id`,
	`u`.`username` AS `username`,
	`u`.`c_id` AS `c_id`,
	if((`pe`.`e_id` is not null),1,0) AS `is_enrolled` 
from 
	((`mycaar_lms`.`program` `p` 
	join 
		`mycaar_lms`.`user` `u` FORCE INDEX (`c_id`) on((`u`.`c_id` = `p`.`company_id` and u.`c_id` = '.Yii::$app->user->identity->c_id.' and `p`.`program_id` = '.$params['program_id'].' ))) 
   left join 
		`mycaar_lms`.`program_enrollment` `pe` on(((`pe`.`program_id` = `p`.`program_id`) and (`pe`.`user_id` = `u`.`id`))))');
		
		$query = $command->queryAll();  */
		
	 
		
        // add conditions that should always apply here
		
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => [
				'pageSize' => 0,
			],
        ]);
			
        $this->load($params);

        if (!$this->validate()) {  
			
            return $dataProvider;
        } 
		$query->andFilterWhere([
            //'id' => $this->id,
            //'is_enrolled' => $this->is_enrolled,          
           
        ]);
        
		//$query->andFilterWhere(['like', 'username', $this->username]);	
		
        return $dataProvider;		
    }
}

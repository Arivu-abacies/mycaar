<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Module;

/**
 * SearchModule represents the model behind the search form about `common\models\Module`.
 */
class SearchModule extends Module
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_id', 'program_id', 'status'], 'integer'],
            [['title', 'short_description', 'featured_video_url', 'detailed_description'], 'safe'],
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
    public function search($params,$program_id=null)
    {
		
        $query = Module::find()->orderBy('module_order');
		if($program_id)
			$query = Module::find()->where(['program_id'=>$program_id])->orderBy('module_order');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'module_id' => $this->module_id,
            'program_id' => $this->program_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'short_description', $this->short_description])
            ->andFilterWhere(['like', 'featured_video_url', $this->featured_video_url])
            ->andFilterWhere(['like', 'detailed_description', $this->detailed_description]);

        return $dataProvider;
    }
	
    public function searchCustom($params,$program_id=null)
    {
		
        $query = Module::find();
		if($program_id)
			$query = Module::find()->where(['program_id'=>$program_id]);
        // add conditions that should always apply here
		else{
			$query->joinWith(['program as p']);
			$query->andFilterWhere(['p.company_id'=>\Yii::$app->user->identity->c_id]);			
		}

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'module_id' => $this->module_id,
            //'program_id' => $this->program_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'module.title', $this->title])
            ->andFilterWhere(['like', 'short_description', $this->short_description])
            ->andFilterWhere(['like', 'featured_video_url', $this->featured_video_url])
            ->andFilterWhere(['like', 'detailed_description', $this->detailed_description]);

        return $dataProvider;
    }
}

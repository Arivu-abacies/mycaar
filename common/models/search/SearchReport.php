<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Report;

/**
 * SearchUserProfile represents the model behind the search form about `common\models\UserProfile`.
 */
class SearchReport extends Report
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit_id','user_id', 'location', 'state'], 'integer'],
            [['firstname', 'lastname'], 'safe'],
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
        $query = Report::find();

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
            'unit_id' => $this->unit_id,
            'user_id' => $this->user_id,
            'location' => $this->location,
            'state' => $this->state,
        ]);

        $query->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'lastname', $this->lastname]);

        return $dataProvider;
    }
}

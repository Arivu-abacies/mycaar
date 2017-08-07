<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Unit;

/**
 * SearchUnit represents the model behind the search form about `common\models\Unit`.
 */
class SearchUnit extends Unit
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit_id', 'module_id', 'status'], 'integer'],
            [['title'], 'safe'],
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
    public function search($params,$m_id=null)
    {
        $query = Unit::find();
		if($m_id)
			$query = Unit::find()->where(['module_id'=>$m_id]);
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
            'module_id' => $this->module_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
	
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */	
    public function searchCustom($params,$m_id=null)
    {
        $query = Unit::find();
		if($m_id)
			$query = Unit::find()->where(['module_id'=>$m_id]);
        // add conditions that should always apply here
		else{
			$query->joinWith(['module.program as p']);
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
            'unit_id' => $this->unit_id,
            'module_id' => $this->module_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'unit.title', $this->title]);

        return $dataProvider;
    }
}

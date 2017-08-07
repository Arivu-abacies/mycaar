<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Division;

/**
 * SearchDivision represents the model behind the search form about `common\models\Division`.
 */
class SearchDivision extends Division
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['division_id', 'company_id'], 'integer'],
            [['title', 'description'], 'safe'],
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
			$query = Division::find();			
		} else if(\Yii::$app->user->can('company_admin'))
		{			
			$query = Division::find()->where(['company_id' =>Yii::$app->user->identity->c_id])->orderBy('title');
		}			

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
            'division_id' => $this->division_id,
            'company_id' => $this->company_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}

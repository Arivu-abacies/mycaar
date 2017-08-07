<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Company;

/**
 * SearchCompany represents the model behind the search form about `common\models\Company`.
 */
class SearchCompany extends Company
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'admin'], 'integer'],
            [['name', 'about_us', 'logo'], 'safe'],
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
        $query = Company::find();

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
            'company_id' => $this->company_id,
            'admin' => $this->admin,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'about_us', $this->about_us])
            ->andFilterWhere(['like', 'logo', $this->logo]);

        return $dataProvider;
    }
}

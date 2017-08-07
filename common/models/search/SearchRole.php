<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Role;

/**
 * SearchRole represents the model behind the search form about `common\models\Role`.
 */
class SearchRole extends Role
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'company_id'], 'integer'],
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
			$query = Role::find();
		} else if(\Yii::$app->user->can('company_admin'))
		{			
			$query = Role::find()->where(['company_id' =>Yii::$app->user->identity->c_id])->orderBy('title');
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
            'role_id' => $this->role_id,
            'company_id' => $this->company_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}

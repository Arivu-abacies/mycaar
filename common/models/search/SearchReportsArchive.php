<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ReportsArchive;

/**
 * SearchReportsArchive represents the model behind the search form about `common\models\ReportsArchive`.
 */
class SearchReportsArchive extends ReportsArchive
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['a_id', 'company_id','program_id'], 'integer'],
            [[ 'archived_date'], 'safe'],
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
        $query = ReportsArchive::find()->orderBy('a_id DESC');

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
            'a_id' => $this->a_id,
            'program_id' => $this->program_id,
            'company_id' => $this->company_id,
            //'archived_date' => $this->archived_date,
        ]);

        $query->andFilterWhere(['like', 'archive_url', $this->archive_url]);
		$query->andFilterWhere(['like', 'archived_date', $this->archived_date]);
		
        return $dataProvider;
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchCustom($params)
    {
        $query = ReportsArchive::find()->where(['reports_archive.company_id'=>\Yii::$app->user->identity->c_id])->orderBy('a_id DESC');;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
		//$query->joinWith(['program as program']);
		
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'a_id' => $this->a_id,
            'program_id' => $this->program_id,
            'reports_archive.company_id' => $this->company_id,
            //'archived_date' => $this->archived_date,
        ]);
		$query->andFilterWhere(['like', 'archived_date', $this->archived_date]);
		//$query->andFilterWhere(['like', 'program.title', $this->program_id]);
        $query->andFilterWhere(['like', 'archive_url', $this->archive_url]);

        return $dataProvider;
    }
}

<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ResetSchedule;

/**
 * SearchResetSchedule represents the model behind the search form about `common\models\ResetSchedule`.
 */
class SearchResetSchedule extends ResetSchedule
{
	public $program;
	public $module;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['s_id','unit_id', 'module','program'], 'integer'],
            [['cron_time', 'actual_time'], 'safe'],
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
        $query = ResetSchedule::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
		$query->innerJoinWith(['unit as u']);
		$query->innerJoinWith(['unit.module as m']);
		$query->innerJoinWith(['unit.module.program as program']);
		
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            's_id' => $this->s_id,
            'u.unit_id' => $this->unit_id,
			'm.module_id' => $this->module,
			'program.program_id' => $this->program,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'cron_time', $this->cron_time])
            ->andFilterWhere(['like', 'actual_time', $this->actual_time]);
			//->andFilterWhere(['like', 'u.title', $this->unit_id])	
			//->andFilterWhere(['like', 'module.title', $this->module]);	
        return $dataProvider;
    }
}

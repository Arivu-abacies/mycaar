<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "awareness_question".
 *
 * @property integer $aq_id
 * @property integer $unit_id
 * @property string $question
 * @property string $question_type
 * @property string $answer
 * @property string $unique_id
 *
 * @property AwarenessAnswer[] $awarenessAnswers
 * @property AwarenessOption[] $awarenessOptions
 * @property Unit $unit
 */
class AwarenessQuestion extends \yii\db\ActiveRecord
{
	public $options;
	public $answers;
	public $isCorrect;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'awareness_question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit_id', 'question', 'answer'], 'required'],
            [['unit_id'], 'integer'],
            [['question'], 'string'],
            [['question_type'], 'string', 'max' => 100],
            [['answer'], 'string', 'max' => 1000],
            [['unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::className(), 'targetAttribute' => ['unit_id' => 'unit_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'aq_id' => 'Aq ID',
            'unit_id' => 'Unit ID',
            'question' => 'Question',
            'question_type' => 'Question Type',
            'answer' => 'Answer',
            'order_id' => 'Order',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAwarenessAnswers()
    {
        return $this->hasMany(AwarenessAnswer::className(), ['question_id' => 'aq_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAwarenessOptions()
    {
        return $this->hasMany(AwarenessOption::className(), ['question_id' => 'aq_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['unit_id' => 'unit_id']);
    }
}

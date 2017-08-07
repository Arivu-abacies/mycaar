<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "awareness_option".
 *
 * @property integer $option_id
 * @property integer $question_id
 * @property string $answer
 *
 * @property AwarenessQuestion $question
 */
class AwarenessOption extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'awareness_option';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'answer'], 'required'],
            [['question_id'], 'integer'],
            [['answer'], 'string'],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => AwarenessQuestion::className(), 'targetAttribute' => ['question_id' => 'aq_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'option_id' => 'Option ID',
            'question_id' => 'Question ID',
            'answer' => 'Answer',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(AwarenessQuestion::className(), ['aq_id' => 'question_id']);
    }
}

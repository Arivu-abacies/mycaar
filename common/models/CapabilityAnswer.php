<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "capability_answer".
 *
 * @property integer $ca_id
 * @property integer $question_id
 * @property integer $user_id
 * @property string $answer
 *
 * @property CapabilityQuestion $question
 * @property User $user
 */
class CapabilityAnswer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'capability_answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'user_id', 'answer'], 'required'],
            [['question_id', 'user_id'], 'integer'],
            [['answer'], 'string', 'max' => 100],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => CapabilityQuestion::className(), 'targetAttribute' => ['question_id' => 'cq_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ca_id' => 'Ca ID',
            'question_id' => 'Question ID',
            'user_id' => 'User ID',
            'answer' => 'Answer',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(CapabilityQuestion::className(), ['cq_id' => 'question_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCapability_question()
    {
        return $this->hasOne(CapabilityQuestion::className(), ['cq_id' => 'question_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}

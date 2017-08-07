<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "awareness_answer".
 *
 * @property integer $aa_id
 * @property integer $question_id
 * @property integer $user_id
 * @property string $answer
 * @property string $created_at
 *
 * @property AwarenessQuestion $question
 * @property User $user
 */
class AwarenessAnswer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'awareness_answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'user_id', 'answer'], 'required'],
            [['question_id', 'user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['answer'], 'string', 'max' => 1000],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => AwarenessQuestion::className(), 'targetAttribute' => ['question_id' => 'aq_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'aa_id' => 'Aa ID',
            'question_id' => 'Question ID',
            'user_id' => 'User ID',
            'answer' => 'Answer',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(AwarenessQuestion::className(), ['aq_id' => 'question_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAwareness_question()
    {
        return $this->hasOne(AwarenessQuestion::className(), ['aq_id' => 'question_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}

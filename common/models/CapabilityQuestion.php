<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "capability_question".
 *
 * @property integer $cq_id
 * @property integer $unit_id
 * @property string $question
 * @property integer $order_id
 *
 * @property CapabilityAnswer[] $capabilityAnswers
 */
class CapabilityQuestion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'capability_question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit_id', 'question', 'order_id'], 'required'],
            [['unit_id', 'order_id'], 'integer'],
            [['question'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cq_id' => 'Cq ID',
            'unit_id' => 'Unit ID',
            'question' => 'Question',
            'order_id' => 'Order ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCapabilityAnswers()
    {
        return $this->hasMany(CapabilityAnswer::className(), ['question_id' => 'cq_id']);
    }
}

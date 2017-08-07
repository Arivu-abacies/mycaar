<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "unit_element".
 *
 * @property integer $element_id
 * @property integer $unit_id
 * @property string $element_type
 * @property integer $element_order
 * @property string $content
 *
 * @property Unit $unit
 */
class UnitElement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unit_element';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit_id', 'element_type', 'element_order'], 'required'],
            [['unit_id', 'element_order'], 'integer'],
            [['content'], 'string'],
            [['element_type'], 'string', 'max' => 1000],
            [['unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::className(), 'targetAttribute' => ['unit_id' => 'unit_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'element_id' => 'Element ID',
            'unit_id' => 'Unit ID',
            'element_type' => 'Element Type',
            'element_order' => 'Element Order',
            'content' => 'Content',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['unit_id' => 'unit_id']);
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "report".
 *
 * @property integer $report_id
 * @property integer $unit_id
 * @property integer $student_id
 * @property integer $awareness_progress
 * @property integer $capability_progress
 * @property string $updated_at
 * @property string $username
 * @property string $email
 * @property integer $c_id
 * @property integer $profile_id
 * @property integer $user_id
 * @property string $employee_number
 * @property string $firstname
 * @property string $lastname
 * @property integer $division
 * @property integer $location
 * @property integer $state
 * @property integer $role
 */
class Report extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_id', 'unit_id', 'student_id', 'awareness_progress', 'capability_progress', 'c_id', 'profile_id', 'user_id', 'division', 'location', 'state', 'role'], 'integer'],
            [['unit_id', 'student_id', 'awareness_progress', 'username', 'email', 'user_id'], 'required'],
            [['updated_at'], 'safe'],
            [['username', 'email'], 'string', 'max' => 255],
            [['employee_number', 'firstname', 'lastname'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'report_id' => 'Report ID',
            'unit_id' => 'Unit ID',
            'student_id' => 'Student ID',
            'awareness_progress' => 'Awareness Progress',
            'capability_progress' => 'Capability Progress',
            'updated_at' => 'Updated At',
            'username' => 'Username',
            'email' => 'Email',
            'c_id' => 'C ID',
            'profile_id' => 'Profile ID',
            'user_id' => 'User ID',
            'employee_number' => 'Employee Number',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'division' => 'Division',
            'location' => 'Location',
            'state' => 'State',
            'role' => 'Role',			
        ];
    }
}

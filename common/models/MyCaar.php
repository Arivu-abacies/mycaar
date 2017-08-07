<?php

namespace common\models;

use Yii;


/**
 * This is the model class for table "company".
 *
 * @property integer $company_id
 * @property string $name
 * @property string $about_us
 * @property string $logo
 * @property integer $admin
 *
 * @property User $admin0
 */
class MyCaar
{
  	/**
     * Return All Users Details Depends Upon Role Name From DB
     */
    public static function getUserAllByrole($rolename)
    {			 	
		$Roleusers = \Yii::$app->authManager->getUserIdsByRole($rolename);		
		$data = User::find()->where(['IN', 'id', $Roleusers])->andWhere(['status'=>10])->all();
		return $data;
    }
	
	public static function getChildRoles($rolename){
		$child_role = [];			
		$child_role_name = \Yii::$app->authManager->getChildRoles($rolename);			
		foreach($child_role_name as $tmp){
			$child_role[$tmp->name] = $tmp->name;
		}
		return $child_role;
	}
	
	public static function getRoleNameByUserid($userid)
	{	// Return by dency Copied from common user model		
		$roles = Yii::$app->authManager->getRolesByUser($userid);
		if (!$roles) {
			return null;
		}
		reset($roles);
		/* @var $role \yii\rbac\Role */
		$role = current($roles);
		return $role->name;
	}
	
	public static function getRandomPassword(){
		$length = 6;
		$chars = array_merge(range(0,9), range('a','z'), range('A','Z'));
		shuffle($chars);
		$password = implode(array_slice($chars, 0, $length));
		return $password;
	}
	
	public static function getChildRolesName($rolename){
		$child_role = [];			
		$child_role_name = \Yii::$app->authManager->getChildRoles($rolename);			
		foreach($child_role_name as $tmp){
			if($rolename != $tmp->name)
			 $child_role[$tmp->name] = $tmp->name;
		}
		return $child_role;
	}
}

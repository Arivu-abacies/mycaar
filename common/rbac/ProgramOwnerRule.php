<?php

namespace common\rbac;

use yii\rbac\Rule;
use common\models\User;
/**
 * Checks if authorID matches user passed via params
 */
class ProgramOwnerRule extends Rule
{
    public $name = 'isProgramOwner';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return isset($params['post']->company_id) ? $params['post']->company_id == \Yii::$app->user->identity->c_id : false;
    }
}
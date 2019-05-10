<?php

namespace sjaakp\pluto\rbac;

use yii\rbac\Rule;
use sjaakp\pluto\models\User;

/**
 * Class RoleRule
 * Checks whether a User as a certain Role (default: 'admin')
 * @package sjaakp\pluto
 */
class RoleRule extends Rule
{
    public $name = 'hasRole';

    /**
     * @param string|int $user the user id.
     * @param \yii\rbac\Item $item the Role or Permission that this Rule is associated with
     * @param $params User|array
     *      -   User
     *      -   [ 'model' => User, 'role' => <string> ]
     *          'role' is optional; default is 'admin'
     * @return bool whether the Rule permits the Role or Permission.
     */
    public function execute($user, $item, $params)
    {
        if (! is_array($params)) $params = ['model' => $params];
        $role = $params['role'] ?? 'admin';
        return in_array($role, $params['model']->getRoleNames());
    }
}

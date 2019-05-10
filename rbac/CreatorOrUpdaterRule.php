<?php

namespace sjaakp\pluto\rbac;

use yii\rbac\Rule;

/**
 * Class CreatorOrUpdaterRule
 * Checks whether a model is created or updated by a user
 * @package sjaakp\pluto
 */
class CreatorOrUpdaterRule extends Rule
{
    public $name = 'isCreatorOrUpdater';

    /**
     * @param string|int $user the user id.
     * @param \yii\rbac\Item $item the Role or Permission that this Rule is associated with
     * @param $params object|array, one of:
     *     -    yii\base\Model
     *     -    [ 'model' => <yii\base\Model>, 'createdAttribute' => <string>, 'updatedAttribute' => <string> ]
     *      'createdAttribute' is optional; default is 'created_by'
     *      'updatedAttribute' is optional; default is 'updated_by'
     * @return bool whether the Rule permits the Role or Permission.
     */
    public function execute($user, $item, $params)
    {
        if (! is_array($params)) $params = ['model' => $params];
        $model = $params['model'];
        $creAttr = $params['createdAttribute'] ?? 'created_by';
        $updAttr = $params['updatedAttribute'] ?? 'updated_by';
        return $user == $model->$creAttr || $user == $model->updAttr;
    }
}

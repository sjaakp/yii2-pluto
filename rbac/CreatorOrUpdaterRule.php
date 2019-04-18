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
     * @param string|int $user the user ID.
     * @param \yii\rbac\Item $item the role or permission that this rule is associated with
     * @param $params string|object, one of:
     *     -    yii\base\Model
     *     -    [ 'model' => <yii\base\Model>, 'createdAttribute' => <string>, 'updatedAttribute' => <string> ]
     *      'createdAttribute' is optional; default is 'created_by'
     *      'updatedAttribute' is optional; default is 'updated_by'
     * @return bool whether the rule permits the role or permission it is associated with.
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

<?php

namespace sjaakp\pluto\rbac;

use yii\rbac\Rule;

/**
 * Class CreatorRule
 * Checks whether a model is created by a user
 * @package sjaakp\pluto
 */
class CreatorRule extends Rule
{
    public $name = 'isCreator';

    /**
     * @param string|int $user the user ID.
     * @param \yii\rbac\Item $item the role or permission that this rule is associated with
     * @param $params string|object, one of:
     *     -    yii\base\Model
     *     -    [ 'model' => <yii\base\Model>, 'attribute' => <string> ]
     *      'attribute' is optional; default is 'created_by'
     * @return bool whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if (! is_array($params)) $params = ['model' => $params];
        $model = $params['model'];
        $attribute = $params['attribute'] ?? 'created_by';
        return $user == $model->$attribute;
    }
}

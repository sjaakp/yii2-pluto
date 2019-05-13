<?php
/**
 * yii2-pluto
 * ----------
 * User management module for Yii2 framework
 * Version 1.0.0
 * Copyright (c) 2019
 * Sjaak Priester, Amsterdam
 * MIT License
 * https://github.com/sjaakp/yii2-pluto
 * https://sjaakpriester.nl
 */

namespace sjaakp\pluto\models;

use Yii;
use yii\base\Model;
use yii\base\ModelEvent;
use yii\helpers\ArrayHelper;

class Permission extends Model
{
    const EVENT_BEFORE_SAVE = 'beforeSave';
    const EVENT_BEFORE_DELETE = 'beforeDelete';

    public $name;
    public $description;
    public $ruleName;
    public $data;
    public $createdAt;
    public $updatedAt;

    public $permChildren = [];      // names
    public $permsAvailable = [];    // [ name => name ]

    protected $oldName;

    /**
     * Permission constructor.
     * @param null $id
     * @param array $config
     */
    public function __construct($id = null, $config = [])
    {
        if (! is_null($id))   {
            $this->loadItem($id);
        }
        $this->loadAvailable($id);
        parent::__construct($config);
    }

    /**
     * @return bool
     */
    public function getIsNew()
    {
        return is_null($this->oldName);
    }

    /**
     * @param bool $runValidation
     * @return bool
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function save($runValidation = true)
    {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_SAVE, $event);
        if (! $event->isValid)  {
            return false;
        }

        if ($runValidation && !$this->validate()) {
            return false;
        }

        $auth = Yii::$app->authManager;

        $item = $this->getOrCreateItem();
        if (! $this->isNew) $auth->removeChildren($item);

        $item->name = $this->name;
        $item->description = $this->description;
        $item->ruleName = $this->ruleName;
        $item->data = $this->data;

        if ($this->isNew) $auth->add($item);
        else $auth->update($this->oldName, $item);

        if ($this->permChildren) {
            foreach($this->permChildren as $id) {
                $child = $auth->getPermission($id);
                $auth->addChild($item, $child);
            }
        }
        return true;
    }

    /**
     * @throws \Exception
     */
    public function delete()
    {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_DELETE, $event);
        if ($event->isValid)  {
            $auth = Yii::$app->authManager;
            $perm = $auth->getPermission($this->name);
            $auth->remove($perm);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'ruleName', 'data' ], 'string'],
            ['ruleName', 'default', 'value' => null],  // without this, integrity violation with DbManager, nasty!
            ['name', 'validateName'],
            [['createdAt', 'updatedAt'], 'safe'],
            ['permChildren', 'safe']
        ];
    }

    /**
     * Validates name.
     * Documentation states that name must be 'globally unique'.
     * I gather that this means that no Item (Role or Permission) can have the same name.
     * @link https://www.yiiframework.com/doc/api/2.0/yii-rbac-item#$name-detail
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateName($attribute, $params)
    {
        if (!$this->hasErrors() && $this->$attribute != $this->oldName) {
            $auth = Yii::$app->authManager;
            if ($auth->getRole($this->$attribute) || $auth->getPermission($this->$attribute))   {
                $this->addError($attribute, "The name '{$this->$attribute}' is already taken.");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('pluto', 'Name'),
            'description' => Yii::t('pluto', 'Description'),
            'ruleName' => Yii::t('pluto', 'Condition'),
            'permChildren' => Yii::t('pluto', 'Included Permissions'),
            'createdAt' => Yii::t('pluto', 'Created at'),
            'updatedAt' => Yii::t('pluto', 'Updated at'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return [
            'name' => Yii::t('pluto', 'The name of this Permission, preferably a verb. There may be no other Role or Permission with this name.'),
            'description' => Yii::t('pluto', 'The description of this Permission.'),
            'ruleName' => Yii::t('pluto', 'The Condition applying to this Permission.'),
            'data' => Yii::t('pluto', 'The additional data associated with the Condition.'),
            'permChildren' => Yii::t('pluto', 'This Permission includes all of the selected Permissions.')
        ];
    }

    /**
     * @param $id
     */
    protected function loadItem($id) {
        $auth = Yii::$app->authManager;
        $perm = $auth->getPermission($id);
        if ($perm)  {
            $this->oldName = $id;
            $this->setAttributes((array) $perm);
            $this->permChildren = array_keys($auth->getChildren($id));
        }
    }

    /**
     * @param $id
     */
    protected function loadAvailable($id)
    {
        $auth = Yii::$app->authManager;
        $this->permsAvailable = ArrayHelper::map($auth->getPermissions(), 'name', 'name');
        if (! is_null($id)) {
            ArrayHelper::remove($this->permsAvailable, $id);     // Permission can't be its own child
        }
    }

    /**
     * @return yii\rbac\Permission|null
     */
    protected function getOrCreateItem()    {
        $auth = Yii::$app->authManager;
        return $this->isNew ? $auth->createPermission($this->name) : $auth->getPermission($this->oldName);
    }
}

<?php


namespace sjaakp\pluto\models;

use Yii;
use yii\base\ModelEvent;
use yii\helpers\ArrayHelper;

class Role extends Permission
{
    public $roleChildren = [];      // names
    public $rolesAvailable = [];    // [ name => name ]

    /**
     * @param bool $runValidation
     * @return bool
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function save($runValidation = true)
    {
        $r = parent::save($runValidation);

        if ($r && $this->roleChildren) {
            $auth = Yii::$app->authManager;
            $item = $auth->getRole($this->name);

            foreach($this->roleChildren as $id) {
                $child = $auth->getRole($id);
                $auth->addChild($item, $child);
            }
        }
        return $r;
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
            $role = $auth->getRole($this->name);
            $auth->remove($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $r = parent::rules();
        $r[] = ['roleChildren', 'safe'];
        return $r;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $r = parent::attributeLabels();
        $r['permChildren'] = Yii::t('pluto', 'Permissions');
        $r['roleChildren'] = Yii::t('pluto', 'Included Roles');
        return $r;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return [
            'name' => Yii::t('pluto', 'The name of this Role, preferably a noun. There may be no other Role or Permission with this name.'),
            'description' => Yii::t('pluto', 'The description of this Role.'),
            'ruleName' => Yii::t('pluto', 'The Condition applying to this Role.'),
            'data' => Yii::t('pluto', 'The additional data associated with the Condition.'),
            'permChildren' => Yii::t('pluto', 'The Permissions belonging to this Role.'),
            'roleChildren' => Yii::t('pluto', 'This Role includes all of the selected Roles.')
        ];
    }

    /**
     * @param $id
     */
    protected function loadItem($id) {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($id);
        if ($role)  {
            $this->oldName = $id;
            $this->setAttributes((array) $role);
            $children = $auth->getChildren($id);
            foreach ($children as $id => $child)   {
                if ($child->type == yii\rbac\Role::TYPE_PERMISSION) $this->permChildren[] = $id;
                else $this->roleChildren[] = $id;
            }
        }
    }

    /**
     * @param $id
     */
    protected function loadAvailable($id)
    {
        $auth = Yii::$app->authManager;
        $this->permsAvailable = ArrayHelper::map($auth->getPermissions(), 'name', 'name');
        $this->rolesAvailable = ArrayHelper::map($auth->getRoles(), 'name', 'name');
        if (! is_null($id)) {
            ArrayHelper::remove($this->rolesAvailable, $id);     // Role can't be its own child
        }
    }

    /**
     * @return yii\rbac\Role|null
     */
    protected function getOrCreateItem()    {
        $auth = Yii::$app->authManager;
        return $this->isNew ? $auth->createRole($this->name) : $auth->getRole($this->oldName);
    }
}

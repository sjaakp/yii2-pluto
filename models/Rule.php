<?php

namespace sjaakp\pluto\models;

use Yii;
use yii\base\Model;
use yii\base\ModelEvent;

class Rule extends Model
{
    const EVENT_BEFORE_DELETE = 'beforeDelete';

    public $name;
    public $createdAt;
    public $updatedAt;

    /**
     * Permission constructor.
     * @param null $id
     * @param array $config
     */
    public function __construct($id = null, $config = [])
    {
        if (! is_null($id))   {
            $this->loadRule($id);
        }
        parent::__construct($config);
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
            $rule = $auth->getRule($this->name);
            $auth->remove($rule);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'createdAt', 'updatedAt'], 'safe'],
        ];
    }

    /**
     * @param $id
     */
    protected function loadRule($id) {
        $auth = Yii::$app->authManager;
        $rule = $auth->getRule($id);
        if ($rule)  {
            $this->setAttributes((array) $rule);
        }
    }
}

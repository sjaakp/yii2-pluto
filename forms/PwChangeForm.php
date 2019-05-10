<?php
namespace sjaakp\pluto\forms;

use Yii;
use yii\base\Model;
use sjaakp\pluto\models\User;

/**
 * Password recover request form
 */
class PwChangeForm extends Model
{
    /* @var $user User */
    public $user;
    public $currentPassword;
    public $flags;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $mod = Yii::$app->controller->module;
        return [
            ['currentPassword', 'required'],
            ['currentPassword', 'validatePassword'],
        ];
    }

    /**
     * Inline validation for password.
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        $r = $this->user->validatePassword($this->$attribute);
        if (! $r) {
            $this->addError($attribute, Yii::t('pluto', 'Incorrect password.'));
        }
    }

    /**
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        $r = parent::validate($attributeNames, $clearErrors);       // first, validate form
        if ($r) $r = $this->user->validate($attributeNames, $clearErrors); // then, User (this sets new password)
        return $r;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'currentPassword' => Yii::t('pluto', 'Old password'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function load($data, $formName = null)
    {
        $r = parent::load($data, 'PwChangeForm');
        if ($r) $r = $this->user->load($data, 'User');
        return $r;
    }
}

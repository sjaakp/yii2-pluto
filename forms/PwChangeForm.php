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
     * Validates password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->user->validatePassword($this->$attribute)) {
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
        $r = $this->user->validate($attributeNames, $clearErrors);
        if ($r) $r = parent::validate($attributeNames, $clearErrors);
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
}

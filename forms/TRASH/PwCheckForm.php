<?php
namespace sjaakp\pluto\forms;

use Yii;
use yii\base\InvalidCallException;
use yii\base\Model;
use sjaakp\pluto\models\User;

/**
 * Password recover request form
 */
class PwCheckForm extends Model
{
    public $password;
    public $password_repeat;
    public $captcha;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $mod = Yii::$app->controller->module;
        return [
            ['password', 'required'],
//            ['password', 'string', 'min' => Yii::$app->params['user.minPasswordLength'] ?? 6],
            ['password', 'match', 'pattern' => $mod->passwordRegexp],

            ['password_repeat', 'required', 'on' => 'pw_repeat'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'on' => 'pw_repeat'],

            ['password', 'validatePassword'],

            ['captcha', 'required', 'on' => 'captcha'],
            ['captcha', 'captcha', 'captchaAction' => $mod->id . '/default/captcha', 'on' => 'captcha'],
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
        $u = Yii::$app->user;
        if ($u->isGuest) {
            throw new InvalidCallException('User is not logged in.');
        }
        /* @var $user User */
        $user = $u->identity;

        if (!$user->validatePassword($this->$attribute)) {
            $this->addError($attribute, 'Incorrect password.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'password' => 'Password',
            'password_repeat' => 'Password (again)',
            'captcha' => 'Verification Code',
        ];
    }
}

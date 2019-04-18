<?php

namespace sjaakp\pluto\forms;

use sjaakp\pluto\Module;
use Yii;
use yii\base\Model;
use sjaakp\pluto\models\User;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $name;
    public $password;
    public $password_repeat;
    public $rememberMe = true;
    public $captcha;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $mod = Module::getInstance();
        return [
            [['name', 'password'], 'required'],
            ['rememberMe', 'boolean'],

            ['password', 'validatePassword'],
            ['password', 'match', 'pattern' => $mod->passwordRegexp],

            ['password_repeat', 'required'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],

            ['captcha', 'required'],
            ['captcha', 'captcha', 'captchaAction' => Yii::$app->controller->module->id . '/default/captcha'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('pluto', 'Username or email'),
            'password' => Yii::t('pluto', 'Password'),
            'password_repeat' => Yii::t('pluto', 'Repeat Password'),
            'rememberMe' => Yii::t('pluto', 'Remember me'),
            'captcha' => Yii::t('pluto', 'Enter verification code')
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('pluto', 'Incorrect name or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided name and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        $duration = $this->rememberMe ? Yii::$app->controller->module->loginStamina : 0;
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $duration);
        }
        
        return false;
    }

    /**
     * Finds user by [[name]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsernameOrEmail($this->name);
        }

        return $this->_user;
    }
}

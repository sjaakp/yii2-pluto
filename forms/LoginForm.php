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

namespace sjaakp\pluto\forms;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use sjaakp\pluto\Module;
use sjaakp\pluto\models\User;
use sjaakp\pluto\models\Captcha;
use sjaakp\pluto\models\Password;

/**
 * Login form
 */
class LoginForm extends Model
{
    use Captcha, Password;

    public $name;
    public $password;
    public $rememberMe = true;
    public $flags;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
//        $mod = Module::getInstance();
        return ArrayHelper::merge([
            [['name', 'password'], 'required'],
            ['password', 'validatePassword'],
            ['rememberMe', 'boolean'],
        ], $this->captchaRules(), $this->passwordRules());
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
     * Inline validation.
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        $mod = Module::getInstance();
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (! $user
                || !$user->isPasswordValid($this->password)
                || (is_string($mod->fenceMode) && ! Yii::$app->authManager->checkAccess($user->id, $mod->fenceMode))) {
                $this->addError($attribute, Yii::t('pluto', 'Incorrect username, email or password, or the user has not been validated with the link sent by email.'));
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

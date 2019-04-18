<?php
namespace sjaakp\pluto\forms;

use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $name;
    public $email;
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
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'unique', 'targetClass' => '\sjaakp\pluto\models\User', 'message' => 'This name has already been taken.'],
            ['name', 'string', 'min' => 2, 'max' => 60],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 128],
            ['email', 'unique', 'targetClass' => '\sjaakp\pluto\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'match', 'pattern' => $mod->passwordRegexp],

            ['password_repeat', 'required', 'on' => 'pw_repeat'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'on' => 'pw_repeat'],

            ['captcha', 'required', 'on' => 'captcha'],
            ['captcha', 'captcha', 'captchaAction' => $mod->id . '/default/captcha', 'on' => 'captcha'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'password_repeat' => 'Repeat Password',
            'captcha' => 'Enter verification code',
        ];
    }
}

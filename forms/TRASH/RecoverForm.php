<?php
namespace sjaakp\pluto\forms;

use Yii;
use yii\base\Model;

/**
 * Password reset form
 */
class RecoverForm extends Model
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
//            ['password', 'string', 'min' => Yii::$app->params['pluto.minPasswordLength'] ?? 6],
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
            'captcha' => 'Verification Code',
        ];
    }
}

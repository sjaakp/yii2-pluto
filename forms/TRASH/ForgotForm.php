<?php
namespace sjaakp\pluto\forms;

use Yii;
use yii\base\Model;
use sjaakp\pluto\models\User;

/**
 * Password recover request form
 */
class ForgotForm extends Model
{
    public $email;
    public $captcha;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 128],
            ['email', 'exist',
                'targetClass' => '\sjaakp\pluto\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with this email address.'
            ],

            ['captcha', 'required', 'on' => 'captcha'],
            ['captcha', 'captcha', 'captchaAction' => Yii::$app->controller->module->id . '/default/captcha', 'on' => 'captcha'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'captcha' => 'Verification Code',
        ];
    }
}

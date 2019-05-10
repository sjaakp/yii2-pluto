<?php
namespace sjaakp\pluto\forms;

use Yii;
use yii\base\Model;
use sjaakp\pluto\Module;
use sjaakp\pluto\models\User;

/**
 * Password recover request form
 */
class EmailForm extends Model
{
    public $email;
    public $captcha;
    public $flags;

    public $status = User::STATUS_ACTIVE;

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
                'filter' => ['status' => $this->status],
                'message' => Yii::t('pluto', 'There is no user with this email address.')
            ],

            ['captcha', 'required', 'when' => function($model) { return $model->flags & Module::PW_CAPTCHA; }],
            ['captcha', 'captcha', 'captchaAction' => Yii::$app->controller->module->id . '/default/captcha', 'when' => function($model) { return $model->flags & Module::PW_CAPTCHA; }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('pluto', 'Email'),
            'captcha' => Yii::t('pluto', 'Verification Code'),
        ];
    }
}

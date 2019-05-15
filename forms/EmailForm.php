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
use sjaakp\pluto\models\User;
use sjaakp\pluto\models\Captcha;

/**
 * Password recover request form
 */
class EmailForm extends Model
{
    use Captcha;

    public $email;
    public $flags;

    public $status = User::STATUS_ACTIVE;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge([
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 128],
            ['email', 'exist',
                'targetClass' => '\sjaakp\pluto\models\User',
                'filter' => ['status' => $this->status],
                'message' => Yii::t('pluto', 'There is no user with this email address.'),
                'except' => ['resend']
            ],
        ], $this->captchaRules());
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

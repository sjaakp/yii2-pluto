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

namespace sjaakp\pluto\models;

use Yii;

/**
 * Trait Captcha
 * @package sjaakp\pluto\models
 */
trait Captcha
{
    public $captcha;
    public $reCaptcha;

    public function captchaRules()
    {
        $r = [];
        if (in_array('captcha', $this->flags))  {
            $r[] = ['captcha', 'required'];
            $r[] = ['captcha', 'captcha', 'captchaAction' => Yii::$app->controller->module->id . '/default/captcha'];
        }
        else if (in_array('reCaptcha', $this->flags))   {
            $r[] = ['reCaptcha', \himiklab\yii2\recaptcha\ReCaptchaValidator2::class,
                'uncheckedMessage' => Yii::t('pluto', 'Please confirm that you are not a bot.')];
        }

        return $r;
    }
}

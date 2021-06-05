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

//use yii\captcha\Captcha;
use common\components\Captcha;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\User */
/* @var $form yii\widgets\ActiveForm */

if (in_array('captcha', $model->flags)): ?>
    <?= $form->field($model, 'captcha')->widget(Captcha::class, [
        'captchaAction' => ['default/captcha'],
        'template' => '<br />{image}{input}',
        'options' => [ 'class' => 'form-control d-inline-block col-4' ],
    ]) ?>
<?php endif;
if (in_array('reCaptcha', $model->flags)): ?>
    <?= $form->field($model, 'reCaptcha')->label(false)->widget(\himiklab\yii2\recaptcha\ReCaptcha2::class) ?>
<?php endif; ?>

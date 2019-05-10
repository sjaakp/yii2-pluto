<?php
use sjaakp\pluto\Module;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\User */
/* @var $form yii\widgets\ActiveForm */

if ($model->flags & Module::PW_CAPTCHA): ?>
    <?= $form->field($model, 'captcha')->widget(Captcha::class, [
        'captchaAction' => ['default/captcha'],
        'template' => '<br />{image}{input}',
        'options' => [ 'class' => 'form-control d-inline-block col-4' ],
    ]) ?>
<?php endif; ?>

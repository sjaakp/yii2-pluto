<?php

use sjaakp\pluto\Module;
use yii\helpers\Html;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\forms\ForgotForm */
/* @var $context yii\web\Controller */
/* @var $form yii\widgets\ActiveForm */

$context = $this->context;
$module = $context->module;
$viewOptions = $module->viewOptions;
$dlgXtra = ($module->dialogExtras['all'] ?? 0) | ($module->dialogExtras[$context->action->id] ?? 0);

$this->title = Yii::t('pluto', 'Forgot password');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::beginTag('div', $viewOptions['row']) ?>
    <?= Html::beginTag('div', $viewOptions['col']) ?>
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="hint-block"><?= Yii::t('pluto',
                'Please fill out your email. A link to reset your password will be sent there.') ?></p>
        <?php $form = $module->formClass::begin(); ?>
            <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'maxlength' => true]) ?>
        <?php if ($dlgXtra & Module::DLG_CAPTCHA): ?>
            <?= $form->field($model, 'captcha')->widget(Captcha::class, [
                'captchaAction' => ['default/captcha'],
                'template' => '<br />{image}{input}',
                'options' => [ 'class' => 'form-control d-inline-block col-4' ],
            ]) ?>
        <?php endif; ?>
            <div class="form-group mt-4">
                <?= Html::submitButton(Yii::t('pluto', 'Send'), $viewOptions['button']) ?>
            </div>
        <?php $module->formClass::end(); ?>
    </div>
</div>

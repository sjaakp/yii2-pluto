<?php

use sjaakp\pluto\Module;
use yii\helpers\Html;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\forms\LoginForm */
/* @var $context yii\web\Controller */
/* @var $form yii\widgets\ActiveForm */

$context = $this->context;
$module = $context->module;
$viewOptions = $module->viewOptions;
$dlgXtra = ($module->dialogExtras['all'] ?? 0) | ($module->dialogExtras[$context->action->id] ?? 0);
$passwordClass = 'sjaakp\pluto\widgets\\';
$passwordClass .= ($dlgXtra & Module::DLG_PW_REVEAL) ? 'RevealPassword' : 'Password';

$this->title = Yii::t('pluto', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::beginTag('div', $viewOptions['row']) ?>
    <?= Html::beginTag('div', $viewOptions['col']) ?>
        <h1><?= Html::encode($this->title) ?></h1>
        <?php $form = $module->formClass::begin(); ?>
            <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'password')->widget($passwordClass) ?>
        <?php if ($dlgXtra & Module::DLG_DOUBLE_PW): ?>
            <?= $form->field($model, 'password_repeat')->widget($passwordClass) ?>
        <?php endif; ?>
            <?= $form->field($model, 'rememberMe')->checkbox() ?>
        <?php if ($dlgXtra & Module::DLG_CAPTCHA): ?>
            <?= $form->field($model, 'captcha')->widget(Captcha::class, [
                'captchaAction' => ['default/captcha'],
                'template' => '<br />{image}{input}',
                'options' => [ 'class' => 'form-control d-inline-block col-4' ],
            ]) ?>
        <?php endif; ?>
            <div class="form-group mt-4">
                <?= Html::submitButton(Yii::t('pluto', 'Login'), $viewOptions['button']) ?>
            </div>
        <?php $module->formClass::end(); ?>
        <hr />
        <p><?= \yii\helpers\Html::a(Yii::t('pluto', 'Register'), ['signup'], array_merge([
                'title' => Yii::t('pluto', 'If you\'re new here'),
            ], $viewOptions['link'])) ?>
            <?= Html::a(Yii::t('pluto', 'Forgot password'), ['forgot'], array_merge([
                'title' => Yii::t('pluto', 'Reset your password'),
            ], $viewOptions['link'])) ?>
            <?= Html::a(Yii::t('pluto', 'Reconfirm'), ['resend'], array_merge([
                'title' => Yii::t('pluto', 'Send me the email with confirmation instructions again'),
            ], $viewOptions['link'])) ?></p>
    </div>
</div>

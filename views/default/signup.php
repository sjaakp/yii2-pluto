<?php

use sjaakp\pluto\Module;
use yii\helpers\Html;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\forms\SignupForm */
/* @var $context yii\web\Controller */
/* @var $form yii\widgets\ActiveForm */

$context = $this->context;
$module = $context->module;
$viewOptions = $module->viewOptions;
$dlgXtra = ($module->dialogExtras['all'] ?? 0) | ($module->dialogExtras[$context->action->id] ?? 0);
$passwordClass = 'sjaakp\pluto\widgets\\';
$passwordClass .= ($dlgXtra & Module::DLG_PW_REVEAL) ? 'RevealPassword' : 'Password';

$this->title = Yii::t('pluto', 'Register');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::beginTag('div', $viewOptions['row']) ?>
    <?= Html::beginTag('div', $viewOptions['col']) ?>
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="hint-block"><?= Yii::t('pluto', 'Please fill out these fields to register at {appname}.', [
                'appname' => Yii::$app->name
            ]) ?>.</p>
        <?php $form = $module->formClass::begin(); ?>
            <?= $form->field($model, 'name')->textInput(['autofocus' => true, 'maxlength' => true]) ?>
            <?= $form->field($model, 'email')->hint(Yii::t('pluto', 'This must be a real email address'))
                ->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'password')->hint($context->module->passwordHint)
                ->widget($passwordClass) ?>
        <?php if ($dlgXtra & Module::DLG_DOUBLE_PW): ?>
            <?= $form->field($model, 'password_repeat')->widget($passwordClass) ?>
        <?php endif; ?>
        <?php if ($dlgXtra & Module::DLG_CAPTCHA): ?>
            <?= $form->field($model, 'captcha')->widget(Captcha::class, [
                'captchaAction' => ['default/captcha'],
                'template' => '<br />{image}{input}',
                'options' => [ 'class' => 'form-control d-inline-block col-4' ],
            ]) ?>
        <?php endif; ?>
        <div class="form-group mt-4">
            <?= Html::submitButton(Yii::t('pluto', 'Register', $viewOptions['button'])) ?>
        </div>
        <?php $module->formClass::end(); ?>
    </div>
</div>

<?php

use yii\helpers\Html;
use yii\captcha\Captcha;
use sjaakp\pluto\Module;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\forms\PwChangeForm */
/* @var $context yii\web\Controller */
/* @var $form yii\widgets\ActiveForm */

$context = $this->context;
$module = $context->module;
$viewOptions = $module->viewOptions;
$dlgXtra = ($module->dialogExtras['all'] ?? 0) | ($module->dialogExtras[$context->action->id] ?? 0);
$passwordClass = 'sjaakp\pluto\widgets\\';
$passwordClass .= ($dlgXtra & Module::DLG_PW_REVEAL) ? 'RevealPassword' : 'Password';

$this->title = Yii::t('pluto', 'Forget me');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::beginTag('div', $viewOptions['row']) ?>
    <?= Html::beginTag('div', $viewOptions['col']) ?>
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="hint-block"><?= Yii::t('pluto',
                'Remove all my personal data from this site permanently.') ?></p>
        <?php $form = $module->formClass::begin(); ?>
            <?= $form->field($model, 'password')->widget($passwordClass,
                [ 'options'  => ['autofocus' => true]]) ?>
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
            <?= Html::submitButton(Yii::t('pluto', 'Forget me'), $viewOptions['button']) ?>
        </div>
        <?php $module->formClass::end(); ?>
    </div>
</div>

<?php

use sjaakp\pluto\Module;
use yii\captcha\Captcha;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\User */
/* @var $context yii\web\Controller */
/* @var $form yii\widgets\ActiveForm */

$context = $this->context;
$module = $context->module;
$viewOptions = $module->viewOptions;
$dlgXtra = ($module->dialogExtras['all'] ?? 0) | ($module->dialogExtras[$context->action->id] ?? 0);
$passwordClass = 'sjaakp\pluto\widgets\\';
$passwordClass .= ($dlgXtra & Module::DLG_PW_REVEAL) ? 'RevealPassword' : 'Password';

$this->title = Yii::t('pluto', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::beginTag('div', $viewOptions['row']) ?>
    <?= Html::beginTag('div', $viewOptions['col']) ?>
        <h1><?= Html::encode($this->title) ?></h1>
        <?php $form = $module->formClass::begin(); ?>
            <fieldset class="border rounded p-3 mb-4 bg-light">
                <legend class="w-auto px-1 bg-white"><?= Yii::t('pluto', 'Change these fields') ?></legend>
                <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'email')->textInput() ?>
            </fieldset>
            <?= $form->field($model, 'password')->widget($passwordClass) ?>
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
            <div class="form-group mb-4">
                <?= Html::submitButton(Yii::t('pluto', 'Save'), $viewOptions['button']) ?>
            </div>
        <?php $module->formClass::end(); ?>
        <hr />
        <p><?= Html::a(Yii::t('pluto', 'Change password'), ['pw-change'], array_merge([
                'title' => Yii::t('pluto', 'Change your password'),
            ], $viewOptions['link'])) ?>
        <?= Html::a(Yii::t('pluto', 'Forget me'), ['delete'], array_merge([
                'title' => Yii::t('pluto', 'Remove all your data from this site'),
            ], $viewOptions['link'])) ?>
        <?= Html::a(Yii::t('pluto', 'Download data'), ['download'], array_merge([
                'title' => Yii::t('pluto', 'Download your personal data from this site'),
            ], $viewOptions['link'])) ?></p>
    </div>
</div>

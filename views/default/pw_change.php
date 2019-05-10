<?php

use yii\helpers\Html;
use sjaakp\pluto\Module;
use sjaakp\pluto\widgets\RevealPassword;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\forms\PwChangeForm */
/* @var $context yii\web\Controller */
/* @var $form yii\widgets\ActiveForm */

$context = $this->context;
$module = $context->module;
$viewOptions = $module->viewOptions;
$dlgXtra = ($module->dialogExtras['all'] ?? 0) | ($module->dialogExtras[$context->action->id] ?? 0);
$passwordClass = 'sjaakp\pluto\widgets\\';
$passwordClass .= ($dlgXtra & Module::PW_REVEAL) ? 'RevealPassword' : 'Password';

$this->title = Yii::t('pluto', 'Change password');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::beginTag('div', $viewOptions['row']) ?>
    <?= Html::beginTag('div', $viewOptions['col']) ?>
        <h1><?= Html::encode($this->title) ?></h1>
        <?php $form = $module->formClass::begin();
if ($model->flags & Module::PW_REVEAL): ?>
            <?= $form->field($model, 'currentPassword')->widget(RevealPassword::class,
                [ 'options'  => ['autofocus' => true]]) ?>
            <?= $form->field($model->user, 'password')
                ->label(Yii::t('pluto', 'New password'))->hint($module->passwordHint)
                ->widget(RevealPassword::class) ?>
        <?php if ($model->flags & Module::PW_DOUBLE): ?>
            <?= $form->field($model->user, 'password_repeat')
                ->label(Yii::t('pluto', 'New password (again)'))
                ->widget(RevealPassword::class) ?>
        <?php endif;
else: ?>
    <?= $form->field($model, 'currentPassword')->passwordInput($options ?? []) ?>
    <?= $form->field($model->user, 'password')
        ->label(Yii::t('pluto', 'New password'))->hint($module->passwordHint)
        ->passwordInput() ?>
    <?php if ($model->flags & Module::PW_DOUBLE): ?>
        <?= $form->field($model->user, 'password_repeat')
            ->label(Yii::t('pluto', 'New password (again)'))
            ->passwordInput() ?>
    <?php endif;
endif; ?>
        <?= $this->render('_captcha', ['model' => $model->user, 'form' => $form]) ?>
        <div class="form-group mt-4">
                <?= Html::submitButton(Yii::t('pluto', 'Save'), $viewOptions['button']) ?>
            </div>
        <?php $module->formClass::end(); ?>
    </div>
</div>

<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\forms\EmailForm */
/* @var $context yii\web\Controller */
/* @var $form yii\widgets\ActiveForm */

$context = $this->context;
$module = $context->module;
$viewOptions = $module->viewOptions;

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
            <?= $this->render('_captcha', ['model' => $model, 'form' => $form]) ?>
            <div class="form-group mt-4">
                <?= Html::submitButton(Yii::t('pluto', 'Send'), $viewOptions['button']) ?>
            </div>
        <?php $module->formClass::end(); ?>
    </div>
</div>

<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\forms\LoginForm */
/* @var $context yii\web\Controller */
/* @var $form yii\widgets\ActiveForm */

$context = $this->context;
$module = $context->module;
$viewOptions = $module->viewOptions;
$pwHint = $module->passwordHint;

$this->title = Yii::t('pluto', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::beginTag('div', $viewOptions['row']) ?>
    <?= Html::beginTag('div', $viewOptions['col']) ?>
        <h1><?= Html::encode($this->title) ?></h1>
        <?php $form = $module->formClass::begin(); ?>
            <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
            <?= $this->render('_password', ['model' => $model, 'form' => $form,'pwHint'=>$pwHint]) ?>
            <?= $form->field($model, 'rememberMe')->checkbox() ?>
            <?= $this->render('_captcha', ['model' => $model, 'form' => $form]) ?>
            <div class="form-group mt-4">
                <?= Html::submitButton(Yii::t('pluto', 'Login'), $viewOptions['button']) ?>
            </div>
        <?php $module->formClass::end(); ?>
        <hr />
        <p>
        <?php if (! $module->fenceMode): ?>
            <?= Html::a(Yii::t('pluto', 'Register'), ['signup'], array_merge([
                'title' => Yii::t('pluto', 'If you\'re new here'),
            ], $viewOptions['link'])) ?>
        <?php endif; ?>
            <?= Html::a(Yii::t('pluto', 'Forgot password'), ['forgot'], array_merge([
                'title' => Yii::t('pluto', 'Reset your password'),
            ], $viewOptions['link'])) ?>
        <?php if (! $module->fenceMode): ?>
            <?= Html::a(Yii::t('pluto', 'Reconfirm'), ['resend'], array_merge([
                'title' => Yii::t('pluto', 'Send me the email with confirmation instructions again'),
            ], $viewOptions['link'])) ?>
        <?php endif; ?>
        </p>
    </div>
</div>

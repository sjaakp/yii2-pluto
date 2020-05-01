<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\forms\PwChangeForm */
/* @var $context yii\web\Controller */
/* @var $form yii\widgets\ActiveForm */

$context = $this->context;
$module = $context->module;
$viewOptions = $module->viewOptions;

$this->title = Yii::t('pluto', 'Forget me');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::beginTag('div', $viewOptions['row']) ?>
    <?= Html::beginTag('div', $viewOptions['col']) ?>
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="hint-block"><?= Yii::t('pluto',
                'Remove all my personal data from this site permanently.') ?></p>
        <?php $form = $module->formClass::begin(); ?>
            <?= $this->render('_password', ['model' => $model, 'form' => $form, 'pwHint'=> $pwHint, 'options' => ['autofocus' => true]]) ?>
            <?= $this->render('_captcha', ['model' => $model, 'form' => $form]) ?>
            <div class="form-group mt-4">
                <?= Html::submitButton(Yii::t('pluto', 'Forget me'), $viewOptions['button']) ?>
            </div>
        <?php $module->formClass::end(); ?>
    </div>
</div>

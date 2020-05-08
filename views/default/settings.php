<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\User */
/* @var $context yii\web\Controller */
/* @var $form yii\widgets\ActiveForm */

$context = $this->context;
$module = $context->module;
$viewOptions = $module->viewOptions;
$pwHint = $module->passwordHint;

$this->title = Yii::t('pluto', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::beginTag('div', $viewOptions['row']) ?>
    <?= Html::beginTag('div', $viewOptions['col']) ?>
        <h1><?= Html::encode($this->title) ?></h1>
        <?php $form = $module->formClass::begin(); ?>
            <fieldset class="border rounded p-3 mb-4">
                <legend class="w-auto px-1 bg-white"><?= Yii::t('pluto', 'Change these fields') ?></legend>
                <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'email')->textInput() ?>
            </fieldset>
            <?= $this->render('_password', ['model' => $model, 'form' => $form, 'pwHint' => $pwHint ]) ?>
            <?= $this->render('_captcha', ['model' => $model, 'form' => $form]) ?>
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

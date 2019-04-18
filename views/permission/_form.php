<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\Permission */
/* @var $rules array */
/* @var $context yii\web\Controller */
/* @var $form yii\widgets\ActiveForm */

$context = $this->context;
$module = $context->module;
$viewOptions = $module->viewOptions;
?>
<?php $form = $module->formClass::begin(); ?>

<?= $form->field($model, 'name')->textInput([ 'autofocus' => true ]) ?>

<?= $form->field($model, 'description')->textInput() ?>

<div class="row mb-3">
    <?= $form->field($model, 'ruleName', [ 'options' => ['class' => 'col-3']])->dropDownList($rules, [
        'prompt' => ''
    ]) ?>
</div>

<!--<?/*= $form->field($model, 'data')->textInput() */?>-->

<?= $this->render('/partials/dualList', [
    'form' => $form,
    'model' => $model,
    'attribute' => 'permChildren',
    'items' => $model->permsAvailable
]) ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('pluto', 'Save'), $viewOptions['button']) ?>
</div>

<?php $module->formClass::end(); ?>

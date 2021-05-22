<?php

use softark\duallistbox\DualListbox;
use yii\helpers\Html;
use sjaakp\pluto\models\User;

/* @var $this yii\web\View */
/* @var $model User */
/* @var $roles string[] */
/* @var $defaultRoles string[] */
/* @var $context yii\web\Controller */
/* @var $form yii\widgets\ActiveForm */

/* @link https://github.com/softark/yii2-dual-listbox */

$context = $this->context;
$module = $context->module;
$viewOptions = $module->viewOptions;
$multipleRoles = $module->multipleRoles;

$stats = [
    User::STATUS_DELETED => Yii::t('pluto', 'deleted'),
    User::STATUS_BLOCKED => Yii::t('pluto', 'blocked'),
    User::STATUS_PENDING => Yii::t('pluto', 'pending'),
    User::STATUS_ACTIVE => Yii::t('pluto', 'active'),
];

$pwHint = $context->module->passwordHint;
if (! $model->isNewRecord) $pwHint .= '; ' . Yii::t('pluto', 'if empty, password will remain unchanged.')
?>

    <?php $form = $module->formClass::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'credits')->textInput([]) ?>

    <?= $form->field($model, 'password')->hint($pwHint)->textInput() ?>

    <div class="row mb-3">
        <?= $form->field($model, 'status', [ 'options' => ['class' => 'col-3']])->dropDownList($stats) ?>
<?php if (! $multipleRoles): ?>
        <?= $form->field($model, 'singleRole', [ 'options' => ['class' => 'col-3']])
                ->dropDownList($roles, ['prompt' => '']) ?>
<?php endif; ?>
    </div>

<?php if ($multipleRoles): ?>

    <?= $this->render('/partials/dualList', [
        'form' => $form,
        'model' => $model,
        'attribute' => 'roles',
        'items' => $roles
    ]) ?>

<?php endif; ?>

<?php if (! empty($defaultRoles)): ?>
    <p><strong>Default Roles: </strong><?= implode(', ', $defaultRoles) ?></p>
<?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('pluto', 'Save'), $viewOptions['button']) ?>
    </div>

    <?php $module->formClass::end(); ?>

<?php

use sjaakp\pluto\Module;
use sjaakp\pluto\widgets\Password;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\User */
/* @var $form yii\widgets\ActiveForm */
/* @var $options array */

if ($model->flags & Module::PW_REVEAL): ?>
    <?= $form->field($model, 'password')->widget(Password::class, ['options' => $options ?? []]) ?>
    <?php if ($model->flags & Module::PW_DOUBLE): ?>
        <?= $form->field($model, 'password_repeat')->widget(Password::class) ?>
    <?php endif;
else: ?>
    <?= $form->field($model, 'password')->passwordInput($options ?? []) ?>
    <?php if ($model->flags & Module::PW_DOUBLE): ?>
        <?= $form->field($model, 'password_repeat')->passwordInput() ?>
    <?php endif;
endif; ?>

<?php
/**
 * yii2-pluto
 * ----------
 * User management module for Yii2 framework
 * Version 1.0.0
 * Copyright (c) 2019
 * Sjaak Priester, Amsterdam
 * MIT License
 * https://github.com/sjaakp/yii2-pluto
 * https://sjaakpriester.nl
 */

use sjaakp\pluto\widgets\Password;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\User */
/* @var $form yii\widgets\ActiveForm */
/* @var $options array */

if (in_array('reveal', $model->flags)): ?>
    <?= $form->field($model, 'password')->widget(Password::class, ['options' => $options ?? []]) ?>
    <?php if (in_array('double', $model->flags)): ?>
        <?= $form->field($model, 'password_repeat')->widget(Password::class) ?>
    <?php endif;
else: ?>
    <?= $form->field($model, 'password')->passwordInput($options ?? []) ?>
    <?php if (in_array('double', $model->flags)): ?>
        <?= $form->field($model, 'password_repeat')->passwordInput() ?>
    <?php endif;
endif; ?>

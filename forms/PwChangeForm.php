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

namespace sjaakp\pluto\forms;

use Yii;
use yii\base\Model;
use sjaakp\pluto\models\User;

class PwChangeForm extends Model
{
    /* @var $user User */
    public $user;
    public $currentPassword;
    public $flags;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['currentPassword', 'required'],
            ['currentPassword', 'validatePassword'],
        ];
    }

    /**
     * Inline validation for password.
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!($this->user->isPasswordValid($this->$attribute))) {
            $this->addError($attribute, Yii::t('pluto', 'Incorrect password'));
        }
    }

    /**
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        $r = parent::validate($attributeNames, $clearErrors);       // first, validate form
        if ($r) $r = $this->user->validate($attributeNames, $clearErrors); // then, User (this sets new password)
        return $r;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'currentPassword' => Yii::t('pluto', 'Old password'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function load($data, $formName = null)
    {
        $r = parent::load($data, 'PwChangeForm');
        if ($r) $r = $this->user->load($data, 'User');
        return $r;
    }
}

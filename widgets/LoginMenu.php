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

namespace sjaakp\pluto\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use sjaakp\pluto\Module;

class LoginMenu extends Widget
{
    /**
     * @var array HTML options for the dropdown
     */
    public $options = [];

    /**
     * @var string route to profile update
     */
    public $profileUpdate = '/profile/update';

    /**
     * @var string
     */
    public $userMaxWidth = '8em';

    /**
     * @throws InvalidConfigException
     */
    public function run()
    {
        $mod = Module::getInstance();
        $pluto = $mod->id;
        $user = Yii::$app->user;

        if ($user->isGuest)   {
            return Html::tag('li', Html::a(Yii::t('pluto','Login'), ["/$pluto/login"], ['class' => 'nav-link']), ['class' => 'nav-item']);
        }
        $liOptions = [
            'class' => 'dropdown nav-item',
        ];
        $aOptions = [
            'data-toggle' => 'dropdown',
            'class' => 'dropdown-toggle nav-link',
            'style' => "max-width:{$this->userMaxWidth};overflow:hidden;whitespace:nowrap;text-overflow:ellipsis;"
        ];

        $manageUsers = $user->can('manageUsers');
        $manageRoles = $user->can('manageRoles');

        $items = [
            [
                'label' => Yii::t('pluto','Settings'),
                'url' => ["/$pluto/settings"],
            ],
            [
                'label' => Yii::t('pluto','Profile Settings'),
                'url' => [$this->profileUpdate, 'id' => $user->id ],
                'visible' => ! is_null($mod->profileClass),
            ],
            '<div class="dropdown-divider"></div>',
            [
                'label' => Yii::t('pluto','Manage Users'),
                'url' => ["/$pluto/user"],
                'visible' => $manageUsers,
            ],
            [
                'label' => Yii::t('pluto','Manage Roles'),
                'url' => ["/$pluto/role"],
                'visible' => $manageRoles,
            ],
        ];
        if ($manageUsers || $manageRoles)   {
            $items[] = '<div class="dropdown-divider"></div>';
        }
        $items[] = [
            'label' => Yii::t('pluto','Logout'),
            'url' => ["/$pluto/logout"],
            'linkOptions' => ['data-method' => 'post']
        ];

        $ddClass = $mod->bootstrapNamespace() . '\Dropdown';
        $dropdown = $ddClass::widget([
            'items' => $items,
            'options' => $this->options
        ]);
        return Html::tag('li', Html::a(Yii::$app->user->identity->name,
                '#', $aOptions) . $dropdown, $liOptions);
    }
}

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

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sjaakp\pluto\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $defaultRoles string[] */
/* @var $context yii\web\Controller */

$context = $this->context;
$viewOptions = $context->module->viewOptions;

$this->title = Yii::t('pluto', 'Users');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('
.filters .form-control {
    font-size: .875em;
    padding: .1rem .5rem;
    height: 2em;
}');
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'name',
            'content' => function($model, $key, $index, $widget)    {
                return Yii::$app->user->can('updateUser', $model) ?
                    Html::a($model->name, [ 'update', 'id' => $model->id ]) : $model->name;
            },
            'format' => 'html',
        ],
        'email:email',
        'statusText',
        'singleRole',
//            'created_at',
//            'updated_at',
        [
            'attribute' => 'lastlogin_at',
            'headerOptions' => [ 'class' => 'sort-ordinal' ]
        ],
        [
            'attribute' => 'login_count',
            'headerOptions' => [ 'class' => 'sort-numerical' ]
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'visibleButtons' => [
                'delete' => function ($model, $key, $index) {
                        return Yii::$app->user->can('updateUser', $model) && Yii::$app->user->id != $model->id;
                                                                        // user can't delete herself
                },
                'update' => function ($model, $key, $index) {
                    return Yii::$app->user->can('updateUser', $model);
                },
            ]
        ],
    ],
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'datetimeFormat' => 'short', // 'dd-MM-yyyy HH:mm:ss'
        'nullDisplay' => '',
    ],
    'tableOptions' => [ 'class' => 'table table-sm table-bordered' ],
    'summary' => '<div class="small text-info">{begin}-{end}/{totalCount}</div>',
    'emptyText' => Yii::t('pluto', 'none'),
    'emptyTextOptions' => [ 'class' => 'small text-info'],
]); ?>

<?php Pjax::end(); ?>

<?php if (! empty($defaultRoles)): ?>
    <p><strong>Default Roles: </strong><?= implode(', ', $defaultRoles) ?></p>
<?php endif; ?>

<p><?= Html::a(Yii::t('pluto', 'New User'), ['create'], $viewOptions['button']) ?>
<?php if (Yii::$app->user->can('manageRoles')): ?>
    <?= Html::a(Yii::t('pluto', 'Roles'), ['role/index'], $viewOptions['link']) ?>
<?php endif; ?></p>

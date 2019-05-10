<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $regData yii\data\BaseDataProvider */
/* @var $unregData yii\data\BaseDataProvider */
/* @var $context yii\web\Controller */

$context = $this->context;
$viewOptions = $context->module->viewOptions;
$buttonOptions = $viewOptions['button'];
$buttonOptions['data-method'] = 'post';

$gridOptions = [
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'datetimeFormat' => 'short', // 'dd-MM-yyyy HH:mm:ss'
        'nullDisplay' => '',
    ],
    'tableOptions' => ['class' => 'table table-sm table-bordered'],
    'summary' => '<div class="small text-info">{begin}-{end}/{totalCount}</div>',
    'emptyText' => Yii::t('pluto', 'none'),
    'emptyTextOptions' => [ 'class' => 'small text-info'],
];

$this->title = Yii::t('pluto', 'Conditions');
$this->breadcrumbs[] = ['label' => Yii::t('pluto', 'Roles'), 'url' => ['role/index']];
$this->breadcrumbs[] = $this->title;
?>
<h1><?= $this->title ?></h1>

<?= GridView::widget(array_merge($gridOptions, [
    'dataProvider' => $regData,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'contentOptions' => [ 'class' => 'text-info' ]
        ],
        'name',
        [
            'label' => Yii::t('pluto', 'Classname'),
            'content' => function($model, $key, $index, $widget)    {
                return get_class($model);
            },

        ],
        'createdAt:datetime',
        'updatedAt:datetime',

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}'
        ],
    ],
])); ?>

<?php if ($unregData->totalCount > 0): ?>

<fieldset class="mt-5">
    <legend><?= Yii::t('pluto', 'Unregistered Conditions') ?></legend>

    <?= GridView::widget(array_merge($gridOptions, [
        'dataProvider' => $unregData,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => [ 'class' => 'text-info' ]
            ],
            'name',
            [
                'label' => Yii::t('pluto', 'Classname'),
                'content' => function($model, $key, $index, $widget)    {
                    return get_class($model);
                },

            ],
        ],
    ])); ?>

    <div class="form-group">
        <?= Html::a(Yii::t('pluto', 'Register'), ['index'], $buttonOptions) ?>
    </div>
</fieldset>

<?php endif; ?>
<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\BaseDataProvider */
/* @var $context yii\web\Controller */

/* @link https://github.com/softark/yii2-dual-listbox */

$context = $this->context;
$viewOptions = $context->module->viewOptions;

$this->title = Yii::t('pluto', 'Permissions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('pluto', 'Roles'), 'url' => ['role/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?></h1>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'contentOptions' => [ 'class' => 'text-info' ]
        ],

        [
            'attribute' => 'name',
            'label' => Yii::t('pluto', 'Name'),
            'content' => function($model, $key, $index, $widget)    {
                return Html::a($model->name, [ 'update', 'id' => $model->name ]);
            },
            'format' => 'html',
        ],
        'description:text:' . Yii::t('pluto', 'Description'),
        'ruleName:text:' . Yii::t('pluto', 'Condition'),
//        'data',
//        'createdAt:datetime',
//        'updatedAt:datetime',

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}'
        ],
    ],
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'datetimeFormat' => 'short', // 'dd-MM-yyyy HH:mm:ss'
        'nullDisplay' => '',
    ],
    'tableOptions' => ['class' => 'table table-sm table-bordered'],
    'summary' => '<div class="small text-info">{begin}-{end}/{totalCount}</div>',
    'emptyText' => Yii::t('pluto', 'none'),
    'emptyTextOptions' => [ 'class' => 'small text-info'],
]); ?>

<p><?= Html::a(Yii::t('pluto', 'New Permission'), ['create'], $viewOptions['button']) ?></p>

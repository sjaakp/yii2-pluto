<?php

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
$this->breadcrumbs[] = $this->title;

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
        'lastlogin_at',
        'login_count',

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
    'tableOptions' => [
        'class' => 'table table-sm table-bordered'],
    ]); ?>

<?php Pjax::end(); ?>

<?php if (! empty($defaultRoles)): ?>
    <p><strong>Default Roles: </strong><?= implode(', ', $defaultRoles) ?></p>
<?php endif; ?>

<p><?= Html::a(Yii::t('pluto', 'New User'), ['create'], $viewOptions['button']) ?>
<?php if (Yii::$app->user->can('manageRoles')): ?>
    <?= Html::a(Yii::t('pluto', 'Roles'), ['role/index'], $viewOptions['button']) ?>
<?php endif; ?></p>

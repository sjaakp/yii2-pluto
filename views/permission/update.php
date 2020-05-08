<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\Permission */
/* @var $rules array */
/* @var $roles array */

$this->title = Yii::t('pluto', 'Update Permission: {permissionname}', [
    'permissionname' => $model->name
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('pluto', 'Roles'), 'url' => ['role/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('pluto', 'Permissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('pluto', 'Update {permissionname}', [
    'permissionname' => $model->name
]);

?>
<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', [
    'model' => $model,
    'rules' => $rules
]) ?>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'createdAt:datetime',
        'updatedAt:datetime',
    ],
    'options' => [
        'tag' => 'dl',
        'class' => 'dl-horizontal small text-muted'
    ],
    'template' => '<dt>{label}</dt><dd>{value}</dd>',
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'datetimeFormat' => 'short'
    ],
]) ?>

<h4><small><?= Yii::t('pluto', 'Roles with this Permission') ?></small></h4>
<?= ListView::widget([
    'dataProvider' => $roles,
    'itemView' => function ($model, $key, $index, $widget) {
        return Html::a($model->name, ['role/update', 'id' => $model->name]);
    },
    'summary' => '<div class="small text-info">{begin}-{end}/{totalCount}</div>',
    'emptyText' => Yii::t('pluto', 'none'),
    'emptyTextOptions' => [ 'class' => 'small text-info'],
]) ?>

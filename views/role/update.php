<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\Role */
/* @var $rules array */
/* @var $users yii\data\ActiveDataProvider */

$this->title = Yii::t('pluto', 'Update Role: {rolename}', [
    'rolename' => $model->name
]);
$this->breadcrumbs[] = ['label' => Yii::t('pluto', 'Roles'), 'url' => ['index']];
$this->breadcrumbs[] = Yii::t('pluto', 'Update {rolename}', [
    'rolename' => $model->name
]);
?>
<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', [
    'model' => $model,
    'rules' => $rules
]) ?>

<hr />
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

<h4><small><?= Yii::t('pluto', 'Users with this Role') ?></small></h4>
<?= ListView::widget([
    'dataProvider' => $users,
    'itemView' => function ($model, $key, $index, $widget) {
        return Html::a($model->name, ['user/update', 'id' => $model->id]);
    },
    'summary' => '<div class="small text-info">{begin}-{end}/{totalCount}</div>',
    'emptyText' => Yii::t('pluto', 'none'),
    'emptyTextOptions' => [ 'class' => 'small text-info'],
]) ?>
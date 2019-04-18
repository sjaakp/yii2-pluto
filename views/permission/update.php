<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\Permission */
/* @var $rules array */
/* @var $users yii\data\ActiveDataProvider */


$this->title = Yii::t('pluto', 'Update Permission: {permissionname}', [
    'permissionname' => $model->name
]);
$this->breadcrumbs[] = ['label' => Yii::t('pluto', 'Roles'), 'url' => ['role/index']];
$this->breadcrumbs[] = ['label' => Yii::t('pluto', 'Permissions'), 'url' => ['index']];
$this->breadcrumbs[] = Yii::t('pluto', 'Update {permissionname}', [
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

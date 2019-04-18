<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\User */
/* @var $roles string[] */
/* @var $defaultRoles string[] */

$this->title = Yii::t('pluto', 'Update user: {username}', [
    'username' => $model->name,
]);
$this->breadcrumbs[] = ['label' => Yii::t('pluto', 'Users'), 'url' => ['index']];
$this->breadcrumbs[] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', [
    'model' => $model,
    'roles' => $roles,
    'defaultRoles' => $defaultRoles,
]) ?>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'created_at:datetime',
        'updated_at:datetime',
        'blocked_at:datetime',
        'lastlogin_at:datetime',
        'login_count'
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

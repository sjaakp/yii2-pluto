<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\User */
/* @var $roles string[] */
/* @var $defaultRoles string[] */

$this->title = Yii::t('pluto', 'New User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('pluto', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', [
    'model' => $model,
    'roles' => $roles,
    'defaultRoles' => $defaultRoles,
]) ?>

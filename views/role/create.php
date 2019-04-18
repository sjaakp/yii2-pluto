<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\Role */
/* @var $rules array */

$this->title = Yii::t('pluto', 'New Role');
$this->breadcrumbs[] = ['label' => Yii::t('pluto', 'Roles'), 'url' => ['index']];
$this->breadcrumbs[] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', [
    'model' => $model,
    'rules' => $rules
]) ?>

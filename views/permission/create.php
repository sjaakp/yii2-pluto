<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sjaakp\pluto\models\Permission */
/* @var $rules array */

$this->title = Yii::t('pluto', 'New Permission');
$this->breadcrumbs[] = ['label' => Yii::t('pluto', 'Roles'), 'url' => ['role/index']];
$this->breadcrumbs[] = ['label' => Yii::t('pluto', 'Permissions'), 'url' => ['index']];
$this->breadcrumbs[] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', [
    'model' => $model,
    'rules' => $rules
]) ?>

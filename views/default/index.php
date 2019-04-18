<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $context yii\web\Controller */

$context = $this->context;
$viewOptions = $context->module->viewOptions;

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= $this->context->action->uniqueId ?></h1>
    <p>
        This is the view content for action "<?= $this->context->action->id ?>".
        The action belongs to the controller "<?= get_class($this->context) ?>"
        in the "<?= $this->context->module->id ?>" module.
    </p>
    <p>
        You may customize this page by editing the following file:<br>
        <code><?= __FILE__ ?></code>
    </p>
<?= $this->render('@app/views/partials/info') ?>

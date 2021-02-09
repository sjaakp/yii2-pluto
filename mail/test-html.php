<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user sjaakp\pluto\models\User */
/* @var $link string */

?>
<div class="confirm-email">
    <p><?= Yii::t('pluto', 'Hello {username},', [
            'username' => $user->name
        ]) ?></p>

    <p><?= Yii::t('pluto', 'This is a test email:') ?></p>
</div>

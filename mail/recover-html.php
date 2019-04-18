<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user sjaakp\pluto\models\User */
/* @var $link string */

?>
<div class="recover-email">
    <p><?= Yii::t('pluto', 'Hello {username},', [
            'username' => $user->name
        ]) ?></p>

    <p><?= Yii::t('pluto', 'Follow the link below to reset your password:') ?></p>

    <p><?= Html::a(Html::encode($link), $link) ?></p>
</div>

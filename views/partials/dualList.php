<?php
use softark\duallistbox\DualListbox;
use sjaakp\pluto\assets\PlutoAsset;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model yii\base\Model */
/* @var $attribute string */
/* @var $items string[] */

/* @link https://github.com/softark/yii2-dual-listbox */

PlutoAsset::register($this);
?>
<fieldset class="mb-3">
    <legend><?= $model->getAttributeLabel($attribute) ?></legend>
<?= $form->field($model, $attribute, [
    'template' => "{input}\n{hint}\n{error}",
    'hintOptions' => ['class' => 'form-text text-muted text-right']
])->widget(DualListbox::class, [
    'items' => $items,
    'options' => [
        'multiple' => true,
        'size' => 8,
    ],
    'clientOptions' => [
        'moveOnSelect' => false,
        'btnClass' => 'btn-sm btn-outline-secondary font-weight-bold',
        'nonSelectedListLabel' => Yii::t('pluto', 'Available'),
        'selectedListLabel' => Yii::t('pluto', 'Selected'),
        'filterTextClear' => Yii::t('pluto', 'Show all'),
        'filterPlaceHolder' => Yii::t('pluto', 'Filter'),
        'moveSelectedLabel' => Yii::t('pluto', 'Move selected'),
        'moveAllLabel' => Yii::t('pluto', 'Move all'),
        'removeSelectedLabel' => Yii::t('pluto', 'Remove selected'),
        'removeAllLabel' => Yii::t('pluto', 'Remove all'),
        'infoText' => Yii::t('pluto', "Showing all {0}"),
        'infoTextFiltered' => Yii::t('pluto', "<span class='text-dark bg-warning'>Filtered</span> {0} from {1}"),
        'infoTextEmpty' => Yii::t('pluto', 'Empty list'),
        'btnMoveText' => '&rsaquo;',
        'btnRemoveText' => '&lsaquo;',
        'btnMoveAllText' => '&raquo;',
        'btnRemoveAllText'=> '&laquo;'
    ],
]) ?>
</fieldset>

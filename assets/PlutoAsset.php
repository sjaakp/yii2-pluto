<?php

namespace sjaakp\pluto\assets;

use yii\web\AssetBundle;

class PlutoAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'assets';

    public $css = [
        'pluto.css'
    ];
    public $depends = [
        'softark\duallistbox\DualListboxAsset',
    ];
}

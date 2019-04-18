<?php

namespace sjaakp\pluto\widgets;

use yii\widgets\InputWidget;
use yii\helpers\Html;

class Password extends InputWidget
{
    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        Html::addCssClass($this->options, 'form-control');
    }


    public function run()
    {
        // display just a simple password input
        return $this->renderInputHtml('password');
    }
}

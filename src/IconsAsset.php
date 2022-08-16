<?php

namespace Cacko\Yii2\Widgets\Video;

use yii\web\AssetBundle;

class IconsAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'icons';

    public $css = [
        'css/widget-video-embedded.css',
        'css/animation.css'
    ];
}

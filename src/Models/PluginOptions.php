<?php

namespace Cacko\Yii2\Widgets\Video\Models;

use JsonSerializable;
use yii\base\Model;

class PluginOptions extends Model implements JsonSerializable
{

    public int $startTime = 0;
    public int $startPosition = 0;
    public bool $autoplay = false;
    public string $embedCode = '';
    public bool $loop = false;
    public bool $lightbox = false;
    public string $videoType = '';
    public string $endImage = '';
    public string $playButton = 'a.play-button';
    public string $muteButton = '.volume.volume-button';
    public string $unmuteButton = '.click-to-unmute';
    public string $volumeButton = 'input.range';
    public string $videoContainer = '.embed-responsive';
    public string $controlsContainerSelector = '.video-controls-container';
    public string $controlsSelector = '.video-controls';
    public string $screenshotContainer = '.screenshot-container';
    public string $widgetContainer = '.widget-container';
    public bool $allowUnmute = true;

    public function jsonSerialize()
    {
        return array_filter($this->getAttributes(), fn ($val) => $val !== null);
    }
}

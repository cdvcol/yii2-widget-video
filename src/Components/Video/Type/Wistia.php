<?php

namespace Cacko\Yii2\Widgets\Video\Components\Video\Type;

use Cacko\Yii2\Widgets\Video\Components\Video\AbstractVideo;
use Cacko\Yii2\Widgets\Video\Components\Video\FetchScreenshotInterface;
use Cacko\Yii2\Widgets\Video\Components\Video\VideoCustomCssClasses;
use Cacko\Yii2\Widgets\Video\Components\Video\VideoFactory;
use Cacko\Yii2\Widgets\Video\components\video\VideoScreenshotInterface;

class Wistia extends AbstractVideo implements VideoScreenshotInterface, FetchScreenshotInterface, VideoCustomCssClasses
{

    const OPTION_VIDEOFOAM = 'videoFoam';
    const OPTION_PLAYBAR = 'playbar';
    const OPTION_PLAYSINLINE = 'playsinline';
    const OPTION_PLAYSUSPENDEDOFFSCREEN = 'playSuspendedOffScreen';
    const OPTION_VOLUMECONTROL = 'volumeControl';
    const OPTION_AUTOPLAY = 'autoPlay';
    const OPTION_SILENTAUTOPLAY = 'silentAutoPlay';
    const OPTION_ENDVIDEOBEHAVIOUR = 'endVideoBehavior';
    const OPTION_SMALLPLAYBUTTON = 'smallPlayButton';
    const OPTION_PLAYBUTTON = 'playButton';
    const OPTION_CONTROLSVISIBLEONLOAD = 'controlsVisibleOnLoad';
    const OPTION_SETTINGSCONTROL = 'settingsControl';
    const OPTION_FULLSCREENBUTTON = 'fullscreenButton';

    public function getEmbedUrl($options = []): string
    {

        $id = $this->getId();

        return 'https://fast.wistia.net/embed/iframe/' . $id . '?' . static::buildQuery($this->getPlayerVars($options));
    }

    public function getId(): ?string
    {
        $url = $this->url;
        if (($questionMarkPos = strpos($url, '?'))) {
            $url = substr($url, 0, $questionMarkPos);
        }
        return substr($url, strrpos($url, '/') + 1);
    }

    public function fetchScreenshot(): bool
    {
        $this->screenshot = \Yii::$app->wistiaApi->getScreenshot($this->getId());

        $this->screenshot->setId($this->getScreenshotId());

        return !empty($this->screenshot->getUrl());
    }


    public function getPlayerVars(): array
    {
        $params = [
            static::OPTION_VIDEOFOAM => true,
            static::OPTION_PLAYBAR => !$this->hideControls,
            static::OPTION_PLAYSINLINE => $this->playsInline || $this->hideControls,
            static::OPTION_PLAYSUSPENDEDOFFSCREEN => false,
            static::OPTION_VOLUMECONTROL => true,
            static::OPTION_AUTOPLAY => $this->autoPlay,
            static::OPTION_SILENTAUTOPLAY => $this->autoPlay ? 'allow' : false,
            static::OPTION_ENDVIDEOBEHAVIOUR => $this->loop ? 'loop' : 'default',
        ];

        if ($this->hideControls) {
            $params[static::OPTION_SMALLPLAYBUTTON] = false;
            $params[static::OPTION_PLAYBUTTON] = false;
            $params[static::OPTION_CONTROLSVISIBLEONLOAD] = false;
            $params[static::OPTION_SETTINGSCONTROL] = false;
            $params[static::OPTION_FULLSCREENBUTTON] = false;
        }
        return $params;
    }


    public function getCustomCssClasses(): array
    {
        return ['wistia_embed', 'wistia_async_' . $this->getId(), ...$this->getCssParams()];
    }

    protected function getCssParams(): array
    {
        $vars = $this->getPlayerVars();
        return array_map(fn ($k, $v) => sprintf('%s=%s', $k, trim(var_export($v, true), '"\'')), array_keys($vars), array_values($vars));
    }

    public function getScreenshotId(): string
    {
        return 'wistia-' . (string)$this->id;
    }

    public function getEmbedType(): string
    {
        return VideoFactory::EMBED_TYPE_CONTAINER;
    }
}

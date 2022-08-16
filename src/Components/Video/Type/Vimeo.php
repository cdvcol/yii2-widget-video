<?php

namespace Cacko\Yii2\Widgets\Video\Components\Video\Type;

use Cacko\Yii2\Widgets\Video\Components\Video\AbstractVideo;
use Cacko\Yii2\Widgets\Video\Components\Video\FetchScreenshotInterface;
use Cacko\Yii2\Widgets\Video\Components\Video\VideoFactory;
use Cacko\Yii2\Widgets\Video\components\video\VideoScreenshotInterface;
use Yii;

class Vimeo extends AbstractVideo implements VideoScreenshotInterface, FetchScreenshotInterface
{

    const OPTION_PORTAIT = 'portrait';
    const OPTION_CONTROLS = 'controls';
    const OPTION_PLAYSINLINE = 'playsinline';
    const OPTION_LOOP = 'loop';
    const OPTION_ID = 'id';
    const OPTION_MUTED = 'muted';
    const OPTION_AUTOPLAY = 'autoplay';
    const OPTION_AUTOPASE = 'autopause';

    public function getPlayerVars(): array
    {
        return [
            static::OPTION_PORTAIT => true,
            static::OPTION_CONTROLS => !$this->hideControls,
            static::OPTION_PLAYSINLINE => $this->playsInline || $this->hideControls,
            static::OPTION_AUTOPASE => false,
            static::OPTION_LOOP => $this->loop,
            static::OPTION_ID => $this->getId(),
            static::OPTION_MUTED => $this->autoPlay,
        ];
    }

    public function getEmbedUrl(): string
    {
        $id = $this->getId();

        if (!$id) {
            return '';
        }

        $queryString = '?' . static::buildQuery($this->getPlayerVars());

        return '//player.vimeo.com/video/' . $id . $queryString;
    }

    public function getCustomDataAttributes(): array
    {
        $vars = $this->getPlayerVars();

        return array_reduce(array_keys($vars), function ($res, $attr) use ($vars) {
            $res['vimeo-' . $attr] = trim(var_export($vars[$attr], true), '"\'');
            return $res;
        }, ['vimeo-id' => $this->getId()]);
    }

    public function getId(): ?string
    {
        return $this->url;
    }

    public function fetchScreenshot(): bool
    {
        $this->screenshot = Yii::$app->vimeoApi->getScreenshot($this->getId());

        $this->screenshot->setId($this->getScreenshotId());

        return !empty($this->screenshot->getUrl());
    }

    public function getScreenshotId(): string
    {
        return 'vimeo-' . (string)$this->id;
    }

    public function getEmbedType(): string
    {
        return VideoFactory::EMBED_TYPE_CONTAINER;
    }
}

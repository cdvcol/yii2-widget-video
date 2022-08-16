<?php

namespace Cacko\Yii2\Widgets\Video\Components\Video\Type;

use Cacko\Yii2\Widgets\Video\Components\Video\AbstractVideo;
use Cacko\Yii2\Widgets\Video\Components\Video\CaptureScreenshotInterface;
use Cacko\Yii2\Widgets\Video\Components\Video\VideoFactory;
use Cacko\Yii2\Widgets\Video\components\video\VideoScreenshotInterface;

class Mp4 extends AbstractVideo implements VideoScreenshotInterface, CaptureScreenshotInterface
{

    const OPTION_CONTROLS = 'controls';
    const OPTION_PLAYSINLINE = 'playsinline';
    const OPTION_MUTED = 'muted';
    const OPTION_AUTOPLAY = 'autoplay';
    const OPTION_LOOP = 'loop';

    public function getPlayerVars(): array
    {
        return [
            static::OPTION_CONTROLS => !$this->hideControls ?: null,
            static::OPTION_PLAYSINLINE => $this->playsInline,
            static::OPTION_MUTED => $this->autoPlay,
            static::OPTION_AUTOPLAY => $this->autoPlay,
            static::OPTION_LOOP => $this->loop ? '' : null,
        ];
    }

    public function getScreenshotId(): string
    {
        return (string)sha1($this->url);
    }

    public function getEmbedType(): string
    {
        return VideoFactory::EMBED_TYPE_VIDEO;
    }
}

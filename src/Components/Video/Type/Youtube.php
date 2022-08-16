<?php

namespace Cacko\Yii2\Widgets\Video\Components\Video\Type;

use Cacko\Yii2\Widgets\Video\Components\Video\AbstractVideo;
use Cacko\Yii2\Widgets\Video\Components\Video\FetchScreenshotInterface;
use Cacko\Yii2\Widgets\Video\Components\Video\VideoFactory;
use Cacko\Yii2\Widgets\Video\components\video\VideoScreenshotInterface;
use Yii;

/**
 *
 * @property-read string $apiType
 */
class Youtube extends AbstractVideo implements VideoScreenshotInterface, FetchScreenshotInterface
{
    const OPTION_LIST_TYPE = 'listType';
    const OPTION_LIST = 'list';
    const OPTION_DISABLE_KB = 'disablekb';
    const OPTION_WMODE = 'wmode';
    const OPTION_MODEST_BRANDING = 'modestbranding';
    const OPTION_CONTROLS = 'controls';
    const OPTION_ENABLE_JS_API = 'enablejsapi';
    const OPTION_PLAYSINLINE = 'playsinline';
    const OPTION_ORIGIN = 'origin';
    const OPTION_LOOP = 'loop';
    const OPTION_MUTED = 'muted';
    const OPTION_AUTOPLAY = 'autoplay';

    const LIST_TYPE_PLAYLIST = 'playlist';
    const WMODE_TRANSPARENT = 'transparent';

    const API_TYPE_PLAYLIST = 'playlists';
    const API_TYPE_VIDEOS = 'videos';

    public function getPlayerVars(): array
    {
        return array_filter([
            static::OPTION_WMODE => static::WMODE_TRANSPARENT,
            static::OPTION_MODEST_BRANDING => $this->hideControls ? 1 : null,
            static::OPTION_CONTROLS => $this->hideControls ? 0 : 1,
            static::OPTION_ENABLE_JS_API => 1,
            static::OPTION_PLAYSINLINE => $this->playsInline || $this->hideControls ? 1 : null,
            static::OPTION_DISABLE_KB => $this->hideControls ? 1 : null,
            static::OPTION_ORIGIN => !empty($this->origin) ? urldecode($this->origin) : null,
            static::OPTION_LIST_TYPE => $this->apiType === static::API_TYPE_PLAYLIST ? 'playlist' : null,
            static::OPTION_LIST => $this->apiType === static::API_TYPE_PLAYLIST ? $this->id : null,
            static::OPTION_LOOP => $this->loop ? 1 : 0,
            static::OPTION_AUTOPLAY => $this->autoPlay ? 1 : 0,
            static::OPTION_ORIGIN => $_SERVER['SERVER_NAME'],
        ], fn ($e) => $e !== null);
    }

    public function getEmbedUrl($options = []): string
    {
        $youtubeId = $this->getId();

        if (!$youtubeId) {
            return '';
        }

        $urlParams = $this->getPlayerVars($options);

        if ($this->apiType === static::API_TYPE_PLAYLIST) {
            $urlParams += [
                static::OPTION_LIST_TYPE => static::LIST_TYPE_PLAYLIST,
                static::OPTION_LIST => $youtubeId,
                static::OPTION_LOOP => $this->loop ? 1 : 0,
            ];
            $youtubeId = '';
        }

        return sprintf('//www.youtube.com/embed/%s?%s', $youtubeId, static::buildQuery($urlParams));
    }

    public function getId(): ?string
    {
        $url = $this->url;
        return preg_match("/(?:\/|%3D|v=|vi=|list=)([0-9A-z-_]{11,41})(?:[%#?&]|$)/i", $url, $match) ? $match[1] : null;
    }

    public function fetchScreenshot(): bool
    {
        $this->screenshot =  Yii::$app->youtubeApi
            ->getScreenshot($this->getId(), $this->apiType)
            ->setId($this->getScreenshotId());

        return !empty($this->screenshot->getUrl());
    }

    protected function getApiType(): string
    {
        if (strpos($this->id, 'PL') === 0) {
            return static::API_TYPE_PLAYLIST;
        }
        return static::API_TYPE_VIDEOS;
    }

    public function getScreenshotId(): string
    {
        return 'youtube-' . (string)$this->id;
    }

    public function getEmbedType(): string
    {
        return VideoFactory::EMBED_TYPE_CONTAINER;
    }
}

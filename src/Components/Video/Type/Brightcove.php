<?php

namespace Cacko\Yii2\Widgets\Video\Components\Video\Type;

use Cacko\Yii2\Widgets\Video\Components\Video\AbstractVideo;
use Cacko\Yii2\Widgets\Video\Components\Video\CaptureScreenshotInterface;
use Cacko\Yii2\Widgets\Video\Components\Video\VideoFactory;
use Cacko\Yii2\Widgets\Video\components\video\VideoScreenshotInterface;

class Brightcove extends AbstractVideo implements VideoScreenshotInterface, CaptureScreenshotInterface
{

    const OPTION_ACCOUNT = 'account';
    const OPTION_VIDEOID = 'video-id';
    const OPTION_PLAYER = 'player';
    const OPTION_EMBED = 'embed';
    const OPTION_JS = 'js';

    protected $accountId = '';

    protected $videoId = '';

    public function init()
    {
        $path = parse_url($this->url, PHP_URL_PATH);
        $this->accountId = trim(strtok($path, '/'), '/');
        $query = parse_url($this->url, PHP_URL_QUERY);
        parse_str($query, $params);
        $this->videoId = $params['videoId'];
        parent::init();
    }

    public function getId(): string
    {
        return $this->videoId;
    }

    public function getPlayerVars(): array
    {
        return [
            static::OPTION_ACCOUNT => $this->accountId,
            static::OPTION_VIDEOID => $this->id,
            static::OPTION_PLAYER => 'default',
            static::OPTION_EMBED => 'default',
            static::OPTION_JS => sprintf('https://players.brightcove.net/%s/default_default/index.min.js', $this->accountId)
        ];
    }


    public function getEmbedType(): string
    {
        return VideoFactory::EMBED_TYPE_VIDEO_JS;
    }

    public function getScreenshotId(): string
    {
        return (string) sha1($this->url);
    }
}

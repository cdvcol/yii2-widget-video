<?php

namespace Cacko\Yii2\Widgets\Video\Components\Video\Type;

use Cacko\Yii2\Widgets\Video\Components\Video\AbstractVideo;
use Cacko\Yii2\Widgets\Video\Components\Video\FetchScreenshotInterface;
use Cacko\Yii2\Widgets\Video\Components\Video\VideoFactory;
use Cacko\Yii2\Widgets\Video\Components\Video\VideoNonInteractiveInterface;
use Cacko\Yii2\Widgets\Video\components\video\VideoScreenshotInterface;
use Yii;

class Twitch extends AbstractVideo implements FetchScreenshotInterface, VideoScreenshotInterface, VideoNonInteractiveInterface
{

    protected $id;
    protected $type;

    const TYPE_CLIP = 'clip';
    const TYPE_COLLECTION = 'collection';
    const TYPE_VIDEO = 'video';
    const TYPE_CHANNEL = 'channel';

    public function isNonInteractive(): bool
    {
        return $this->type == static::TYPE_CLIP;
    }

    public function init()
    {
        $url = $this->url;

        $host = parse_url($url, PHP_URL_HOST);
        $path = parse_url($url, PHP_URL_PATH);

        if (strpos($host, 'clips') === 0) {
            $this->type = static::TYPE_CLIP;
            $this->id = trim($path, '/');
        } else {
            switch ($id = strtok($path, '/')) {
                case 'collections':
                    $this->type = static::TYPE_COLLECTION;
                    $this->id = strtok('/');
                    break;

                case 'videos':
                    $this->type = static::TYPE_VIDEO;
                    $this->id = strtok('/');
                    break;

                default:
                    $this->type = static::TYPE_CHANNEL;
                    $this->id = $id;
            }
        }
        parent::init();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmbedUrl(): string
    {
        $params = [
            'parent' => $this->origin,
            'muted' => (bool)$this->autoPlay,
            'autoplay' => (bool)$this->autoPlay
        ];

        switch ($this->type) {

            case static::TYPE_CLIP:
                $path = 'https://clips.twitch.tv/embed';
                $params['clip'] = $this->id;
                $params['muted'] = false;
                break;

            default:
                $path = 'https://player.twitch.tv';
                $params[$this->type] = $this->id;
        }

        return sprintf('%s/?%s', $path, static::buildQuery($params));
    }

    public function getPlayerVars(): array
    {
        return [
            $this->type => $this->id,
            'height' => '100%',
            'width' => '100%',
            'parent' => $this->origin,
            'muted' => (bool) $this->autoPlay,
            'autoplay' => (bool)$this->autoPlay
        ];
    }

    public function getEmbedType(): string
    {
        return $this->type === static::TYPE_CLIP ? VideoFactory::EMBED_TYPE_IFRAME : VideoFactory::EMBED_TYPE_CONTAINER;
    }

    public function fetchScreenshot(): bool
    {
        try {
            switch ($this->type) {
                case static::TYPE_CLIP:
                    $res = Yii::$app->twitchApi->getCLip($this->id);
                    break;

                case static::TYPE_VIDEO:
                    $res = Yii::$app->twitchApi->getVideo($this->id);
                    break;

                default:
                    $res = Yii::$app->twitchApi->getChannel($this->id);
                    break;
            }

            $result = $res ? $res['thumbnail_url'] : '';

            if ($result) {
                $result = str_replace(['%{width}', '%{height}'], [640, 360], $result);
            }

            $this->screenshot =  $this->screenshot->setUrl($result)->setId($this->getScreenshotId());
            return !empty($this->screenshot->getUrl());
        } catch (\Exception $e) {
            \Yii::warning($e->getMessage(), 'twitch.api');
            return false;
        }
    }

    public function getScreenshotId(): string
    {
        return 'twitch-' . $this->id;
    }
}

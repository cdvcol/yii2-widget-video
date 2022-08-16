<?php

namespace Cacko\Yii2\Widgets\Video\Components\Video;

use Cacko\Yii2\Widgets\Video\Models\Screenshot;
use Cacko\Yii2\Widgets\Video\Models\ScreenshotInterface;
use yii\base\Component;


abstract class AbstractVideo extends Component implements VideoInterface, VideoScreenshotInterface
{
    public string $url = '';

    public bool $autoPlay = false;

    public bool $hideControls = false;

    public int $startTimestamp = 0;

    public int $startPosition = 0;

    public bool $openInModal = false;

    public bool $loop = false;

    public string $placeholderImage  = '';

    public string $placeholderEndImage = '';

    public string $origin = '';

    protected bool $playsInline = true;

    protected ScreenshotInterface $screenshot;

    public function __construct(ScreenshotInterface $screenshot, $config = [])
    {
        $this->screenshot = $screenshot;
        parent::__construct($config);
    }

    public function init()
    {
        $this->screenshot = Screenshot::findOne($this->getScreenshotId()) ?: $this->screenshot;
        if (empty($this->screenshot->getUrl()) && $this instanceof FetchScreenshotInterface && $this->fetchScreenshot()) {
            $this->screenshot->save();
        }
    }

    public function getEmbedUrl(): string
    {
        return $this->url;
    }

    public function getType(): string
    {
        return strtolower((new \ReflectionClass(get_called_class()))->getShortName());
    }

    public function getId(): ?string
    {
        return '';
    }

    protected static function buildQuery($params = []): string
    {
        return http_build_query(array_reduce(array_keys($params), function ($res, $property) use ($params) {
            $value = $params[$property];
            if ($value !== null) {
                $res[$property] = trim(var_export($value, true), '"\'');
            }
            return $res;
        }, []));
    }

    public function setPlaysInline($mode): void
    {
        $this->playsInline = (bool)$mode;
    }

    public function getScreenshot(): ScreenshotInterface
    {
        return $this->screenshot;
    }

    public function getDefaultScreenshot(): string
    {
        return Screenshot::DEFAULT_SCREENSHOT;
    }
}

<?php

namespace Cacko\Yii2\Widgets\Video\Widgets;

use Cacko\Yii2\Widgets\FullScreen\components\Options;
use Cacko\Yii2\Widgets\Video\Components\Video\AbstractVideo;
use Cacko\Yii2\Widgets\Video\Components\Video\CaptureScreenshotInterface;
use Cacko\Yii2\Widgets\Video\Components\Video\VideoCustomCssClasses;
use Cacko\Yii2\Widgets\Video\Components\Video\VideoFactory;
use Cacko\Yii2\Widgets\Video\Components\Video\VideoInterface;
use Cacko\Yii2\Widgets\Video\Components\Video\VideoNonInteractiveInterface;
use Cacko\Yii2\Widgets\Video\Components\Video\VideoScreenshotInterface;
use Cacko\Yii2\Widgets\FullScreen\FullScreenAsset;
use Cacko\Yii2\Widgets\Video\Models\PluginOptions as PluginOptions;
use Cacko\Yii2\Widgets\Video\Models\Screenshot;
use Cacko\Yii2\Widgets\Video\VideoAsset;
use ReflectionClass;
use ReflectionProperty;
use UAParser\Parser;
use UAParser\Result\Client;
use UAParser\Result\OperatingSystem;
use yii\base\Widget;
use Yii;

/**
 * 
 * @package Cacko\Yii2\Widgets\Video\Widgets
 * @property-read string $videoType
 * @property-read string $embedUrl
 * @property-read string $videoId
 * @property-read array $playerVars
 * @property-read string $defaultScreenshot
 * @property-read bool $requireScreenshotCapture
 * @property-read string $screenshotCaptureId
 * @property-read string $embedType
 * @property-read array $customCssClasses
 * @property-read bool $isNonInteractive
 */
class VideoWidget extends Widget
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

    /** @var Client */
    protected $ua;

    protected VideoInterface $video;

    const FLIPPING_IPHONE = ['iOS'];

    public function init()
    {
        $this->origin = $_SERVER['HTTP_HOST'];
        $reflect = new ReflectionClass($this);
        $properties = array_keys(get_class_vars(AbstractVideo::class));
        $props   = array_reduce($reflect->getProperties(ReflectionProperty::IS_PUBLIC), function ($res, ReflectionProperty $prop) use ($properties) {
            if (in_array($prop->getName(), $properties)) {
                $res[$prop->getName()] = $prop->getValue($this);
            }
            return $res;
        }, []);
        $this->video = VideoFactory::instance($props);
    }

    public function run()
    {
        $pluginOptions = new PluginOptions([
            'startTime' => $this->startTimestamp * 1000,
            'startPosition' => $this->startPosition,
            'autoplay' => $this->autoPlay,
            'embedCode' => $this->videoEmbedCode,
            'loop' => $this->loop,
            'lightbox' => $this->openInModal,
            'videoType' => $this->videoType,
            'allowUnmute' => (bool) (in_array($this->videoType, [VideoFactory::YOUTUBE, VideoFactory::TWITCH]) | !$this->isFlippingIphone),
            'endImage' => $this->render('screenshot', [
                'screenshot' => $this->placeholderEndImage,
                'noContainer' => true
            ]),
        ]);

        if (!$this->isFlippingIphone) {
            FullScreenAsset::registerWidget($this, $this->getFullScreenOptions());
        }

        VideoAsset::registerWidget($this, $pluginOptions);

        return $this->render('run.php');
    }

    /**
     * 
     * @return \Cacko\Yii2\Widgets\FullScreen\components\Options 
     * @throws \yii\base\InvalidConfigException 
     */
    public function getFullScreenOptions(): Options
    {
        return Yii::createObject([
            'class' => Options::class,
            'selectorFullScreen' => '.widget-container .embed-responsive-16by9'
        ]);
    }

    /**
     * 
     * @return string 
     * @throws \yii\base\InvalidArgumentException 
     */
    public function getVideoEmbedCode(): string
    {
        $video = $this->video;

        $embedType = $video->embedType;

        return $this->render($embedType);
    }

    /**
     * 
     * @return bool 
     */
    public function getIsFlippingIphone(): bool
    {
        return in_array($this->os->family, static::FLIPPING_IPHONE);
    }

    /**
     * 
     * @return \UAParser\Result\OperatingSystem 
     * @throws \UAParser\Exception\FileNotFoundException 
     */
    protected function getOs(): OperatingSystem
    {
        if (!$this->ua) {
            /** @var Parser $parser */
            $parser = Parser::create();
            $userAgent = \Yii::$app->request->userAgent;
            $this->ua = $parser->parse($userAgent);
        }
        return $this->ua->os;
    }

    /**
     * 
     * @param string $key 
     * @return mixed 
     * @throws \yii\base\UnknownPropertyException 
     * @throws \yii\base\InvalidCallException 
     */
    public function __get($key)
    {

        $video = $this->video;

        switch ($key) {
            case 'videoType':
                return $video->type;

            case 'embedUrl':
                return $video->embedUrl;

            case 'embedType':
                return $video->embedType;

            case 'videoId':
                return $video->id;

            case 'playerVars':
                return $video->playerVars;

            case 'screenshot':
                return $video->screenshot->getUrl();

            case 'defaultScreenshot':
                return $video instanceof VideoScreenshotInterface ? $video->getDefaultScreenshot() : Screenshot::DEFAULT_SCREENSHOT;

            case 'requireScreenshotCapture':
                return $video instanceof CaptureScreenshotInterface;

            case 'screenshotCaptureId':
                return $video instanceof VideoScreenshotInterface ? $video->getScreenshotId() : null;

            case 'customCssClasses':
                return $video instanceof VideoCustomCssClasses ? $video->getCustomCssClasses() : [];

            case 'isNonInteractive':
                return $video instanceof VideoNonInteractiveInterface && $video->isNonInteractive();

            default:
                return parent::__get($key);
        }
    }
}

<?php

namespace Cacko\Yii2\Widgets\Video\Components\Video;

use Cacko\Yii2\Widgets\Video\Components\Video\Type\Brightcove;
use Cacko\Yii2\Widgets\Video\Components\Video\Type\Mp4;
use Cacko\Yii2\Widgets\Video\Components\Video\Type\Twitch;
use Cacko\Yii2\Widgets\Video\Components\Video\Type\Vimeo;
use Cacko\Yii2\Widgets\Video\Components\Video\Type\Wistia;
use Cacko\Yii2\Widgets\Video\Components\Video\Type\Youtube;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use yii\helpers\Json;
use yii\web\JsExpression;
use Yii;
use yii\web\UnsupportedMediaTypeHttpException;

class VideoFactory
{

    const OPTION_START_TIME = 'startTime';

    const YOUTUBE = 'youtube';
    const VIMEO = 'vimeo';
    const WISTIA = 'wistia';
    const TWITCH = 'twitch';
    const MP4 = 'mp4';
    const BRIGHTCOVE = 'brightcove';

    const EMBED_TYPE_CONTAINER = 'container';
    const EMBED_TYPE_IFRAME = 'iframe';
    const EMBED_TYPE_VIDEO = 'video';
    const EMBED_TYPE_VIDEO_JS = 'video-js';

    const SUPPORTED_TYPES = [
        self::YOUTUBE => 'YouTube',
        self::VIMEO => 'Vimeo',
        self::WISTIA => 'Wistia',
        self::TWITCH => 'Twitch',
        self::BRIGHTCOVE => 'Brightcove',
        self::MP4 => 'HTML5 Video in MP4 container'
    ];

    const URL_TYPE_PATTERNS = [
        Youtube::class => ['youtu\.be', 'youtube\.com'],
        Vimeo::class => ['vimeo\.com'],
        Twitch::class => ['twitch\.tv'],
        Wistia::class => ['wistia\.com', 'wi\.st', 'wistia\.net'],
        Mp4::class => ['\.mp4$'],
        Brightcove::class => ['\.brightcove\.net'],
    ];

    public static function instance(array $config): VideoInterface
    {
        $url = $config['url'];

        $class = static::getClass($url);

        if (!$class || !class_exists($class)) {
            throw new UnsupportedMediaTypeHttpException("$url is not supported.");
        }
        return Yii::createObject(array_merge(
            [
                'class' => $class
            ],
            $config
        ));
    }

    protected static function getClass(string $url): string
    {
        $iterator = new RecursiveArrayIterator(static::URL_TYPE_PATTERNS);
        $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
        $class = '';
        foreach ($recursive as $k => $v) {
            if (is_string($k)) {
                $class = $k;
            } elseif (preg_match('#' . $v . '#i', $url)) {
                return $class;
            }
        }
        return '';
    }

    public static function jsGetVideoType(): string
    {
        $patterns = array_reduce(array_keys(static::URL_TYPE_PATTERNS), function ($res, $type) {
            $res[strtolower((new \ReflectionClass($type))->getShortName())] = array_map(function ($p) {
                return new JsExpression("new RegExp('${p}', 'i')");
            }, static::URL_TYPE_PATTERNS[$type]);
            return $res;
        }, []);

        return '
            const PATTERNS = ' . Json::encode($patterns) . ';
            getVideoType = (url) => {
                for (const [type, patterns] of Object.entries(PATTERNS)) {
                    if (patterns.some(p => p.test(url))) {
                        return type;
                    }
                }
                return "unknown";
            };
        ';
    }
}

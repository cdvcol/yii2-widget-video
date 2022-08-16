<?php

namespace Cacko\Yii2\Widgets\Video;

use Cacko\Yii2\RequireJs\RequireJsAsset;
use Cacko\Yii2\Widgets\FullScreen\FullScreenAsset;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\helpers\Json;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use Cacko\Yii2\Widgets\Video\Models\PluginOptions;
use yii\base\Widget;

class VideoAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'video';


    public $js = [
        'js/widget.video.js',
    ];

    public $css = [
        'css/widget.video.scss'
    ];

    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
        RequireJsAsset::class,
        FullScreenAsset::class,
        IconsAsset::class,
    ];


    public static function registerWidget(Widget $widget, PluginOptions $options): \yii\web\AssetBundle
    {

        $view = $widget->view;
        $widgetId = $widget->id;
        $options = Json::encode($options);

        $bundle = static::register($view);
        $view->registerJs(sprintf("$('#%s').widgetVideo(%s);", $widgetId, $options));

        return $bundle;
    }


    public function registerAssetFiles($view)
    {

        $am = $view->getAssetManager();
        $path = $am->getAssetUrl($this, 'js');

        $config = [
            'baseUrl' => $path,
            'paths' => [
                'players' => './players',
                "twitch" => "https://player.twitch.tv/js/embed/v1",
                "vimeo" => "https://player.vimeo.com/api/player",
                "wistia" => "https://fast.wistia.com/assets/external/E-v1",
                "brightcove" => "./lib/brightcove-player-loader",
                "youtube" => "https://www.youtube.com/iframe_api?noext"
            ],
            "shim" => [
                "youtube" => ["exports" => "YT"],
                "players/vimeo" => ["deps" => ["vimeo"]],
                "players/wistia" => ["deps" => ["wistia"]],
                "players/twitch" => ["deps" => ["twitch"]],
                "players/brightcove" => ["deps" => ["brightcove"]],
                "players/youtube" => ["deps" => ["youtube"]],
            ]
        ];

        $view->registerJs('
        (function(require, define, requirejs) {
          require.config(' . Json::encode($config) . ');
          }(__require.require,__require.define,__require.requirejs));
        ');

        parent::registerAssetFiles($view);
    }
}

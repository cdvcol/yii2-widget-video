<?php

namespace Cacko\Yii2\Widgets\Video;

use Cacko\Yii2\Widgets\Video\Components\Twitch\Api as TwitchApi;
use Cacko\Yii2\Widgets\Video\Components\YouTube\Api as YouTubeApi;
use Cacko\Yii2\Widgets\Video\Components\Vimeo\Api as VimeoApi;
use Cacko\Yii2\Widgets\Video\Components\Wistia\Api as WistiaApi;
use Cacko\Yii2\Widgets\Video\Controller\Controller;
use Cacko\Yii2\Widgets\Video\Controller\ControllerInterface;
use Cacko\Yii2\Widgets\Video\Models\ScreenshotInterface;
use Cacko\Yii2\Widgets\Video\Models\Screenshot;
use yii\base\BootstrapInterface;
use Yii;
use yii\web\AssetConverter;

class Bootstrap implements BootstrapInterface
{

    const APIS = [
        'twitchApi' => [
            'class' => TwitchApi::class,
            'clientId' => '',
            'secretId' => '',
        ],
        'youtubeApi' => [
            'class' => YouTubeApi::class,
            'youtubeKey' => '',
        ],
        'vimeoApi' => [
            'class' => VimeoApi::class
        ],
        'wistiaApi' => [
            'class' => WistiaApi::class
        ]
    ];

    public function bootstrap($app)
    {
        $components = $app->components;
        Yii::$container->set(AssetConverter::class, ['commands' => ['scss' => ['css', 'pscss --sourcemap  {from} > {to}']]]);
        foreach (self::APIS as $id => $defintion) {
            if (!array_key_exists($id, $components)) {
                $app->set($id, $defintion);
            }
        }

        if (!Yii::$container->has(ScreenshotInterface::class)) {
            Yii::$container->set(ScreenshotInterface::class, Screenshot::class);
        }

        if (!Yii::$container->has(ControllerInterface::class)) {
            Yii::$container->set(ControllerInterface::class, Controller::class);
        }

        if ($app instanceof \yii\web\Application) {
            $urlManager = $app->getUrlManager();
            $urlManager->enablePrettyUrl = true;
            $app->controllerMap[ControllerInterface::CONTROLLER_ID] = ['class' => ControllerInterface::class];
        }
    }
}

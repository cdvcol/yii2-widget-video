<?php

namespace Cacko\Yii2\Widgets\Video\Components\YouTube;

use Cacko\Yii2\Widgets\Video\Models\ScreenshotInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\base\Component;
use yii\helpers\Json;

class Api extends Component
{

    public $youtubeKey = '';

    protected ScreenshotInterface $screenshot;

    public function __construct(ScreenshotInterface $screenshot, $config = [])
    {
        parent::__construct($config);
        $this->screenshot = $screenshot;
    }


    public function getScreenshot($videoId, $apiType): ScreenshotInterface
    {

        try {
            $client = new Client([
                'base_uri' => 'https://www.googleapis.com/youtube/v3/'
            ]);
            $res = $client->get(sprintf('%s?%s', $apiType, http_build_query([
                'key' => $this->youtubeKey,
                'part' => 'snippet',
                'id' => $videoId
            ])));
            $body = Json::decode($res->getBody());
            $item = reset($body['items']);

            $thumbnails = (array)$item['snippet']['thumbnails'];
            uasort($thumbnails, fn ($a, $b) => $a['width'] <=> $b['width']);
            $thumb = array_pop($thumbnails);
            return $this->screenshot->setUrl($thumb['url']);
        } catch (RequestException $e) {
            \Yii::warning($e->getMessage(), 'youtube.api');
            return $this->screenshot;
        }
    }
}

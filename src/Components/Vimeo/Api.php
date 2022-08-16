<?php

namespace Cacko\Yii2\Widgets\Video\Components\Vimeo;

use Cacko\Yii2\Widgets\Video\Models\ScreenshotInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\base\Component;
use yii\helpers\Json;

class Api extends Component
{

    protected Client $client;

    protected ScreenshotInterface $screenshot;

    const THUMBNAIL_URL = 'thumbnail_url';

    public function __construct(ScreenshotInterface $screenshot, $config = [])
    {
        $this->screenshot = $screenshot;
        parent::__construct($config);
    }

    public function init()
    {
        $this->client = new Client(['base_uri' => 'https://vimeo.com/api/']);
        parent::init();
    }

    public function getScreenshot(string $id): ScreenshotInterface
    {
        $res = $this->get('oembed.json',  ['url' => sprintf('https://vimeo.com/%s', $id)]);

        if (empty($res) || !array_key_exists(static::THUMBNAIL_URL, $res)) {
            return $this->screenshot;
        }

        return $this->screenshot->setUrl($res[static::THUMBNAIL_URL]);
    }

    protected function get($path, $params): array
    {
        try {
            $client = $this->client;
            $res = $client->get($path, ['query' => $params]);
            return (array) Json::decode($res->getBody());
        } catch (RequestException $e) {
            return [];
        }
    }
}

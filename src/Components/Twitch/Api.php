<?php

namespace Cacko\Yii2\Widgets\Video\Components\Twitch;


use GuzzleHttp\Client;
use yii\base\Component;
use yii\helpers\Json;


class Api extends Component
{

    protected $client;

    public $clientId = '';

    public $secretId = '';

    /** @var Oauth */
    protected $oauth;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->oauth = new Oauth([
            'clientId' => $this->clientId,
            'secretId' => $this->secretId,
        ]);
    }

    public function getChannel(string $id)
    {
        $res = $this->getClient()->get('search/channels', ['query' => ['query' => $id]]);

        $result = Json::decode((string)$res->getBody());

        $data = $result['data'];

        return reset($data);
    }

    protected function getClient(): Client
    {
        if (!$this->client) {

            $this->client = new Client([
                'base_uri' => 'https://api.twitch.tv/helix/',
                'headers' => [
                    'Client-id' => $this->clientId,
                    'Authorization' => 'Bearer ' . $this->oauth->getToken()
                ],
            ]);
        }

        return $this->client;
    }

    public function getCLip($id)
    {
        $res = $this->getClient()->get('clips', ['query' => ['id' => $id]]);

        $result = Json::decode((string)$res->getBody());

        $data = $result['data'];

        return reset($data);
    }

    public function getVideo($id)
    {
        $res = $this->getClient()->get('videos', ['query' => ['id' => $id]]);

        $result = Json::decode((string)$res->getBody());

        $data = $result['data'];

        return reset($data);

    }


}
<?php

namespace Cacko\Yii2\Widgets\Video\Components\Twitch;


use GuzzleHttp\Client;
use yii\base\Component;
use yii\helpers\Json;

class Oauth extends Component
{

    public $clientId;

    public $secretId;

    protected $client;

    const SCOPES = ['channel_read user_read user:read:email'];

    public function getToken(): string
    {
        $client = $this->getClient();

        $res = $client->post('token', [
            'query' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->secretId,
                'grant_type' => 'client_credentials',
            ]
        ]);

        $response = Json::decode((string)$res->getBody());

        return $response['access_token'];


    }

    protected function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client([
                'base_uri' => 'https://id.twitch.tv/oauth2/',
            ]);
        }

        return $this->client;
    }

}
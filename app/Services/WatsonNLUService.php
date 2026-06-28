<?php

namespace App\Services;

use GuzzleHttp\Client;

class WatsonNLUService
{
    protected $client;
    protected $apiKey;
    protected $url;

    public function __construct()
    {
        $this->apiKey = env('WATSON_NLU_API_KEY');
        $this->url = env('WATSON_NLU_URL');
        $this->client = new Client();
    }

    public function analyze(string $text): array
    {
        $response = $this->client->post($this->url . '/v1/analyze?version=2022-04-07', [
            'auth' => ['apikey', $this->apiKey],
            'verify' => false, // ← only this line was added
            'json' => [
                'text' => $text,
                'features' => [
                    'keywords' => [
                        'limit' => 20
                    ],
                    'categories' => new \stdClass(),
                    'sentiment' => new \stdClass(),
                ]
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
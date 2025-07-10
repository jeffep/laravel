<?php

namespace App\Services;

use GuzzleHttp\Client;

class WyzeService
{
    protected $client;
    protected $keyId;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://auth-prod.api.wyze.com/api/user/login', // Replace with the actual base URI
        ]);
        $this->keyId = config('services.wyze.key_id');
        $this->apiKey = config('services.wyze.api_key');
    }

    public function controlZone($zoneId, $action)
    {
        $endpoint = $action === 'on' ? 'sprinkler/turn_on' : 'sprinkler/turn_off';
        $response = $this->client->post($endpoint, [
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
                'X-Api-Key-Id' => $this->keyId, // Include key ID if required by API
            ],
            'json' => [
                'zone_id' => $zoneId, // Adjust parameter names as per API documentation
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    // Additional methods for other API calls
}


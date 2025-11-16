<?php

namespace LeMukarram\VectorSearch\VectorStores\Drivers;

use GuzzleHttp\Client;
use LeMukarram\VectorSearch\Contracts\VectorStoreDriver;

class UpstashDriver implements VectorStoreDriver
{
    protected Client $client;
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $this->config['url'],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config['token'],
                'Content-Type' => 'application/json',
            ],
            'timeout' => 10,
        ]);
    }

    public function upsert(array $vectors): bool
    {
        // FIX: Upstash strictly requires the key 'vector' for the float array.
        // Our internal DTO uses 'values', so we map it manually here.
        $payload = array_map(fn($vector) => [
            'id' => $vector->id,
            'vector' => $vector->values, // The critical fix
            'metadata' => $vector->metadata,
        ], $vectors);

        $response = $this->client->post('upsert', ['json' => $payload]);
        return $response->getStatusCode() === 200;
    }

    public function query(array $vector, int $topK, array $filter = []): array
    {
        $payload = [
            'vector' => $vector,
            'topK' => $topK,
            'includeMetadata' => true,
        ];
        
        // Note: Filter implementation depends on specific driver needs
        // if (!empty($filter)) {
        //     $payload['filter'] = $filter;
        // }
        
        $response = $this->client->post('query', ['json' => $payload]);
        $data = json_decode($response->getBody()->getContents(), true);

        // Transform Upstash response to our standard format
        return array_map(fn($result) => [
            'metadata' => $result['metadata'] ?? [],
            'score' => $result['score'],
        ], $data['result']);
    }

    public function delete(array $ids): bool
    {
        // Upstash delete endpoint accepts an array of strings directly
        $response = $this->client->post('delete', ['json' => $ids]);
        return $response->getStatusCode() === 200;
    }
}
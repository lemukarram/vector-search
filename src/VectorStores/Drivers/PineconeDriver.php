<?php

namespace LeMukarram\VectorSearch\VectorStores\Drivers;

use GuzzleHttp\Client;
use LeMukarram\VectorSearch\Contracts\VectorStoreDriver;

class PineconeDriver implements VectorStoreDriver
{
    protected Client $client;
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $this->config['host'],
            'headers' => [
                'Api-Key' => $this->config['api_key'],
                'Content-Type' => 'application/json',
            ],
            'timeout' => 10,
        ]);
    }

    public function upsert(array $vectors): bool
    {
        $payload = [
            'vectors' => array_map(fn($v) => $v->toArray(), $vectors),
            // 'namespace' => 'optional-namespace'
        ];
        $response = $this->client->post('vectors/upsert', ['json' => $payload]);
        return $response->getStatusCode() === 200;
    }

    public function query(array $vector, int $topK, array $filter = []): array
    {
        $payload = [
            'vector' => $vector,
            'topK' => $topK,
            'includeMetadata' => true,
        ];
        // ToDo: Pinecone filter support
        // if (!empty($filter)) {
        //     $payload['filter'] = $filter;
        // }

        $response = $this->client->post('query', ['json' => $payload]);
        $data = json_decode($response->getBody()->getContents(), true);
        
        // Transform data to standard format
        return array_map(fn($match) => [
            'metadata' => $match['metadata'],
            'score' => $match['score'],
        ], $data['matches']);
    }

    public function delete(array $ids): bool
    {
        $response = $this->client->post('vectors/delete', ['json' => ['ids' => $ids]]);
        return $response->getStatusCode() === 200;
    }
}
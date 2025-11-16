<?php

namespace LeMukarram\VectorSearch\VectorStores\Drivers;

use GuzzleHttp\Client;
use LeMukarram\VectorSearch\Contracts\VectorStoreDriver;

class ChromaDriver implements VectorStoreDriver
{
    protected Client $client;
    protected array $config;
    protected string $collection;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->collection = $this->config['collection'] ?? 'laravel-rag';
        $this->client = new Client([
            'base_uri' => "http://{$this->config['host']}:{$this->config['port']}/api/v1/",
            'timeout' => 10,
            'headers' => ['Content-Type' => 'application/json']
        ]);
        // ToDo: Check if collection exists, create if not
    }

    public function upsert(array $vectors): bool
    {
        $payload = [
            'ids' => array_map(fn($v) => $v->id, $vectors),
            'embeddings' => array_map(fn($v) => $v->values, $vectors),
            'metadatas' => array_map(fn($v) => $v->metadata, $vectors),
        ];
        
        $url = 'collections/' . $this->collection . '/upsert';
        $response = $this->client->post($url, ['json' => $payload]);
        return $response->getStatusCode() === 200;
    }

    public function query(array $vector, int $topK, array $filter = []): array
    {
        $payload = [
            'query_embeddings' => [$vector],
            'n_results' => $topK,
            'include' => ['metadatas', 'distances'],
        ];
        // ToDo: Chroma filter support
        // if (!empty($filter)) {
        //     $payload['where'] = $filter;
        // }

        $url = 'collections/' . $this->collection . '/query';
        $response = $this->client->post($url, ['json' => $payload]);
        $data = json_decode($response->getBody()->getContents(), true);

        // Transform data to standard format
        $results = [];
        if (!empty($data['ids'][0])) {
            foreach ($data['ids'][0] as $index => $id) {
                $results[] = [
                    'metadata' => $data['metadatas'][0][$index],
                    'score' => $data['distances'][0][$index], // Chroma uses distance
                ];
            }
        }
        return $results;
    }

    public function delete(array $ids): bool
    {
        $url = 'collections/' . $this->collection . '/delete';
        $response = $this->client->post($url, ['json' => ['ids' => $ids]]);
        return $response->getStatusCode() === 200;
    }
}
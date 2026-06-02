<?php

namespace LeMukarram\VectorSearch\VectorStores\Drivers;

use Illuminate\Support\Facades\Http;
use LeMukarram\VectorSearch\Contracts\VectorStoreDriver;
use LeMukarram\VectorSearch\Exceptions\VectorSearchException;

class PineconeDriver implements VectorStoreDriver
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function client()
    {
        return Http::withHeaders([
            'Api-Key' => $this->config['api_key'],
            'Content-Type' => 'application/json',
        ])->baseUrl($this->config['host']);
    }

    public function upsert(array $vectors): bool
    {
        $payload = [
            'vectors' => array_map(fn($v) => $v->toArray(), $vectors),
        ];
        
        $response = $this->client()->post('vectors/upsert', $payload);

        if ($response->failed()) {
            throw VectorSearchException::apiError('Pinecone', $response->body(), $response->status());
        }

        return $response->successful();
    }

    public function query(array $vector, int $topK, array $filter = []): array
    {
        $payload = [
            'vector' => $vector,
            'topK' => $topK,
            'includeMetadata' => true,
        ];

        $response = $this->client()->post('query', $payload);

        if ($response->failed()) {
            throw VectorSearchException::apiError('Pinecone', $response->body(), $response->status());
        }

        $data = $response->json();
        
        return array_map(fn($match) => [
            'metadata' => $match['metadata'],
            'score' => $match['score'],
        ], $data['matches']);
    }

    public function delete(array $ids): bool
    {
        $response = $this->client()->post('vectors/delete', ['ids' => $ids]);
        return $response->successful();
    }
}

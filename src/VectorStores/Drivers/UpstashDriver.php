<?php

namespace LeMukarram\VectorSearch\VectorStores\Drivers;

use Illuminate\Support\Facades\Http;
use LeMukarram\VectorSearch\Contracts\VectorStoreDriver;
use LeMukarram\VectorSearch\Exceptions\VectorSearchException;

class UpstashDriver implements VectorStoreDriver
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config['token'],
            'Content-Type' => 'application/json',
        ])->baseUrl($this->config['url']);
    }

    public function upsert(array $vectors): bool
    {
        $payload = array_map(fn($vector) => [
            'id' => $vector->id,
            'vector' => $vector->values,
            'metadata' => $vector->metadata,
        ], $vectors);

        $response = $this->client()->post('upsert', $payload);

        if ($response->failed()) {
            throw VectorSearchException::apiError('Upstash', $response->body(), $response->status());
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
            throw VectorSearchException::apiError('Upstash', $response->body(), $response->status());
        }

        $data = $response->json();

        return array_map(fn($result) => [
            'metadata' => $result['metadata'] ?? [],
            'score' => $result['score'],
        ], $data['result']);
    }

    public function delete(array $ids): bool
    {
        $response = $this->client()->post('delete', $ids);
        return $response->successful();
    }
}

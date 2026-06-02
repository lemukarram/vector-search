<?php

namespace LeMukarram\VectorSearch\VectorStores\Drivers;

use Illuminate\Support\Facades\Http;
use LeMukarram\VectorSearch\Contracts\VectorStoreDriver;
use LeMukarram\VectorSearch\Exceptions\VectorSearchException;

class ChromaDriver implements VectorStoreDriver
{
    protected array $config;
    protected string $collection;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->collection = $this->config['collection'] ?? 'laravel-rag';
    }

    protected function client()
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->baseUrl("http://{$this->config['host']}:{$this->config['port']}/api/v1/");
    }

    public function upsert(array $vectors): bool
    {
        $payload = [
            'ids' => array_map(fn($v) => $v->id, $vectors),
            'embeddings' => array_map(fn($v) => $v->values, $vectors),
            'metadatas' => array_map(fn($v) => $v->metadata, $vectors),
        ];
        
        $url = 'collections/' . $this->collection . '/upsert';
        $response = $this->client()->post($url, $payload);

        if ($response->failed()) {
            throw VectorSearchException::apiError('Chroma', $response->body(), $response->status());
        }

        return $response->successful();
    }

    public function query(array $vector, int $topK, array $filter = []): array
    {
        $payload = [
            'query_embeddings' => [$vector],
            'n_results' => $topK,
            'include' => ['metadatas', 'distances'],
        ];

        $url = 'collections/' . $this->collection . '/query';
        $response = $this->client()->post($url, $payload);

        if ($response->failed()) {
            throw VectorSearchException::apiError('Chroma', $response->body(), $response->status());
        }

        $data = $response->json();

        $results = [];
        if (!empty($data['ids'][0])) {
            foreach ($data['ids'][0] as $index => $id) {
                $results[] = [
                    'metadata' => $data['metadatas'][0][$index],
                    'score' => $data['distances'][0][$index],
                ];
            }
        }
        return $results;
    }

    public function delete(array $ids): bool
    {
        $url = 'collections/' . $this->collection . '/delete';
        $response = $this->client()->post($url, ['ids' => $ids]);
        return $response->successful();
    }
}

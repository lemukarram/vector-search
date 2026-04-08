<?php

namespace LeMukarram\VectorSearch\Tests\Feature;

use Illuminate\Support\Facades\Cache;
use LeMukarram\VectorSearch\Contracts\AiChatDriver;
use LeMukarram\VectorSearch\Contracts\AiEmbeddingDriver;
use LeMukarram\VectorSearch\Contracts\VectorStoreDriver;
use LeMukarram\VectorSearch\Core\AiModelManager;
use LeMukarram\VectorSearch\Core\VectorStoreManager;
use LeMukarram\VectorSearch\Facades\VectorSearch;
use LeMukarram\VectorSearch\Tests\TestCase;
use Mockery;

class VectorSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_similar_method_uses_caching()
    {
        $mockEmbedding = Mockery::mock(AiEmbeddingDriver::class);
        $mockEmbedding->shouldReceive('embed')
            ->once()
            ->with('test query')
            ->andReturn([0.1, 0.2]);

        $mockStore = Mockery::mock(VectorStoreDriver::class);
        $mockStore->shouldReceive('query')
            ->twice() // Called twice but embed only once due to cache
            ->andReturn([]);

        // Register mocks in managers
        app(AiModelManager::class)->extend('mock_model', function() use ($mockEmbedding) {
            return new \LeMukarram\VectorSearch\AiModels\AiModel($mockEmbedding, Mockery::mock(AiChatDriver::class));
        });
        app(VectorStoreManager::class)->extend('mock_store', function() use ($mockStore) {
            return $mockStore;
        });

        // First call - should trigger embed
        VectorSearch::similar('test query');

        // Second call - should use cached embedding
        VectorSearch::similar('test query');
    }

    public function test_chat_method_uses_caching()
    {
        $mockEmbedding = Mockery::mock(AiEmbeddingDriver::class);
        $mockEmbedding->shouldReceive('embed')->once()->andReturn([0.1]);

        $mockChat = Mockery::mock(AiChatDriver::class);
        $mockChat->shouldReceive('chat')
            ->once()
            ->andReturn('cached response');

        $mockStore = Mockery::mock(VectorStoreDriver::class);
        $mockStore->shouldReceive('query')->once()->andReturn([]);

        app(AiModelManager::class)->extend('mock_model', function() use ($mockEmbedding, $mockChat) {
            return new \LeMukarram\VectorSearch\AiModels\AiModel($mockEmbedding, $mockChat);
        });
        app(VectorStoreManager::class)->extend('mock_store', function() {
            return Mockery::mock(VectorStoreDriver::class, ['query' => []]);
        });

        // First call - triggers all
        $response1 = VectorSearch::chat('hello');
        
        // Second call - should be fully cached
        $response2 = VectorSearch::chat('hello');

        $this->assertEquals('cached response', $response1);
        $this->assertEquals('cached response', $response2);
    }
}

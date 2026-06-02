<?php

namespace LeMukarram\VectorSearch\Tests\Feature;

use Illuminate\Support\Facades\Cache;
use LeMukarram\VectorSearch\Contracts\AiChatDriver;
use LeMukarram\VectorSearch\Contracts\AiEmbeddingDriver;
use LeMukarram\VectorSearch\Contracts\VectorStoreDriver;
use LeMukarram\VectorSearch\Core\AiModelManager;
use LeMukarram\VectorSearch\Core\AiResponse;
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
        
        config(['vector-search.models.mock_model' => ['driver' => 'mock']]);
        config(['vector-search.stores.mock_store' => ['driver' => 'mock']]);
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
            ->twice()
            ->andReturn([]);

        app(AiModelManager::class)->extend('mock', function() use ($mockEmbedding) {
            return new \LeMukarram\VectorSearch\AiModels\AiModel($mockEmbedding, Mockery::mock(AiChatDriver::class));
        });
        app(VectorStoreManager::class)->extend('mock', function() use ($mockStore) {
            return $mockStore;
        });

        VectorSearch::similar('test query');
        VectorSearch::similar('test query');
    }

    public function test_chat_method_uses_caching()
    {
        $mockEmbedding = Mockery::mock(AiEmbeddingDriver::class);
        $mockEmbedding->shouldReceive('embed')->once()->andReturn([0.1]);

        $mockChat = Mockery::mock(AiChatDriver::class);
        $mockChat->shouldReceive('chat')
            ->once()
            ->andReturn(new AiResponse('cached response'));

        $mockStore = Mockery::mock(VectorStoreDriver::class);
        $mockStore->shouldReceive('query')->once()->andReturn([]);

        app(AiModelManager::class)->extend('mock', function() use ($mockEmbedding, $mockChat) {
            return new \LeMukarram\VectorSearch\AiModels\AiModel($mockEmbedding, $mockChat);
        });
        app(VectorStoreManager::class)->extend('mock', function() use ($mockStore) {
            return $mockStore;
        });

        $response1 = VectorSearch::chat('hello');
        $response2 = VectorSearch::chat('hello');

        $this->assertEquals('cached response', $response1->content());
        $this->assertEquals('cached response', $response2->content());
    }
}

<?php

namespace LeMukarram\VectorSearch\Tests\Feature;

use Illuminate\Support\Facades\Http;
use LeMukarram\VectorSearch\Contracts\AiChatDriver;
use LeMukarram\VectorSearch\Contracts\AiEmbeddingDriver;
use LeMukarram\VectorSearch\Core\AiModelManager;
use LeMukarram\VectorSearch\Core\AiResponse;
use LeMukarram\VectorSearch\Core\VectorStoreManager;
use LeMukarram\VectorSearch\Exceptions\VectorSearchException;
use LeMukarram\VectorSearch\Facades\VectorSearch;
use LeMukarram\VectorSearch\Tests\TestCase;
use Mockery;

class CoreArchitectureTest extends TestCase
{
    public function test_chat_returns_ai_response_and_replaces_placeholders()
    {
        config(['vector-search.rag.system_prompt' => 'Context: {{context}} Query: {{query}}']);
        config(['vector-search.models.mock_model' => ['driver' => 'mock']]);
        config(['vector-search.stores.mock_store' => ['driver' => 'mock']]);

        $mockEmbedding = Mockery::mock(AiEmbeddingDriver::class);
        $mockEmbedding->shouldReceive('embed')->andReturn([0.1]);

        $mockChat = Mockery::mock(AiChatDriver::class);
        $mockChat->shouldReceive('chat')
            ->once()
            ->with('hello', 'Context: No relevant context found. Query: hello')
            ->andReturn(new AiResponse('AI Answer', ['usage' => ['tokens' => 10]]));

        $mockStore = Mockery::mock(\LeMukarram\VectorSearch\Contracts\VectorStoreDriver::class);
        $mockStore->shouldReceive('query')->andReturn([]);

        app(AiModelManager::class)->extend('mock', function() use ($mockEmbedding, $mockChat) {
            return new \LeMukarram\VectorSearch\AiModels\AiModel($mockEmbedding, $mockChat);
        });

        app(VectorStoreManager::class)->extend('mock', function() use ($mockStore) {
            return $mockStore;
        });

        $response = VectorSearch::chat('hello');

        $this->assertInstanceOf(AiResponse::class, $response);
        $this->assertEquals('AI Answer', $response->content());
        $this->assertEquals(['tokens' => 10], $response->usage());
    }

    public function test_drivers_use_http_facade_and_throw_exceptions()
    {
        Http::fake([
            'api.openai.com/*' => Http::response(['error' => 'Invalid API Key'], 401),
        ]);

        $driver = new \LeMukarram\VectorSearch\AiModels\Drivers\OpenAiDriver([
            'api_key' => 'wrong',
            'embedding_model' => 'text-embedding-3-small',
        ]);

        $this->expectException(VectorSearchException::class);
        $this->expectExceptionMessage('[OpenAI] API Error');

        $driver->embed('test');
    }
}

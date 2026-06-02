<?php

namespace LeMukarram\VectorSearch\AiModels\Drivers;

use Illuminate\Support\Facades\Http;
use LeMukarram\VectorSearch\Contracts\AiChatDriver;
use LeMukarram\VectorSearch\Contracts\AiEmbeddingDriver;
use LeMukarram\VectorSearch\Core\AiResponse;
use LeMukarram\VectorSearch\Exceptions\VectorSearchException;

class DeepSeekDriver implements AiChatDriver, AiEmbeddingDriver
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config['api_key'],
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->baseUrl('https://api.deepseek.com/');
    }

    public function embed(string $text): array
    {
        $response = $this->client()->post('embeddings', [
            'model' => $this->config['embedding_model'],
            'input' => $text,
        ]);

        if ($response->failed()) {
            throw VectorSearchException::apiError('DeepSeek', $response->body(), $response->status());
        }

        $data = $response->json();
        return $data['data'][0]['embedding'];
    }

    public function chat(string $prompt, string $systemMessage = ''): AiResponse
    {
        $response = $this->client()->post('chat/completions', [
            'model' => $this->config['chat_model'],
            'messages' => [
                ['role' => 'system', 'content' => $systemMessage ?: 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $prompt]
            ],
        ]);

        if ($response->failed()) {
            throw VectorSearchException::apiError('DeepSeek', $response->body(), $response->status());
        }

        $data = $response->json();

        return new AiResponse(
            $data['choices'][0]['message']['content'],
            [
                'model' => $data['model'],
                'usage' => [
                    'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                    'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
                    'total_tokens' => $data['usage']['total_tokens'] ?? 0,
                ],
                'finish_reason' => $data['choices'][0]['finish_reason'] ?? null,
            ]
        );
    }
}

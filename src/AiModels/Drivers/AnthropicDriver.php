<?php

namespace LeMukarram\VectorSearch\AiModels\Drivers;

use Illuminate\Support\Facades\Http;
use LeMukarram\VectorSearch\Contracts\AiChatDriver;
use LeMukarram\VectorSearch\Core\AiResponse;
use LeMukarram\VectorSearch\Exceptions\VectorSearchException;

class AnthropicDriver implements AiChatDriver
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function client()
    {
        return Http::withHeaders([
            'x-api-key' => $this->config['api_key'],
            'anthropic-version' => $this->config['version'] ?? '2023-06-01',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->baseUrl('https://api.anthropic.com/v1/');
    }

    public function chat(string $prompt, string $systemMessage = ''): AiResponse
    {
        $payload = [
            'model' => $this->config['chat_model'],
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => $this->config['max_tokens'] ?? 1024,
        ];

        if ($systemMessage) {
            $payload['system'] = $systemMessage;
        }

        $response = $this->client()->post('messages', $payload);

        if ($response->failed()) {
            throw VectorSearchException::apiError('Anthropic', $response->body(), $response->status());
        }

        $data = $response->json();

        return new AiResponse(
            $data['content'][0]['text'],
            [
                'model' => $data['model'],
                'usage' => [
                    'prompt_tokens' => $data['usage']['input_tokens'] ?? 0,
                    'completion_tokens' => $data['usage']['output_tokens'] ?? 0,
                    'total_tokens' => ($data['usage']['input_tokens'] ?? 0) + ($data['usage']['output_tokens'] ?? 0),
                ],
                'stop_reason' => $data['stop_reason'] ?? null,
            ]
        );
    }
}

<?php

namespace LeMukarram\VectorSearch\AiModels\Drivers;

use Illuminate\Support\Facades\Http;
use LeMukarram\VectorSearch\Contracts\AiChatDriver;
use LeMukarram\VectorSearch\Contracts\AiEmbeddingDriver;
use LeMukarram\VectorSearch\Core\AiResponse;
use LeMukarram\VectorSearch\Exceptions\VectorSearchException;

class GeminiDriver implements AiChatDriver, AiEmbeddingDriver
{
    protected array $config;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function client()
    {
        return Http::withHeaders([
            'x-goog-api-key' => $this->config['api_key'],
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);
    }

    public function embed(string $text): array
    {
        $url = $this->baseUrl . $this->config['embedding_model'] . ':embedContent';
        
        $response = $this->client()->post($url, [
            'content' => [
                'parts' => [['text' => $text]]
            ],
            'outputDimensionality' => 768,
        ]);

        if ($response->failed()) {
            throw VectorSearchException::apiError('Gemini', $response->body(), $response->status());
        }

        $data = $response->json();
        return $data['embedding']['values'];
    }

    public function chat(string $prompt, string $systemMessage = ''): AiResponse
    {
        $url = $this->baseUrl . $this->config['chat_model'] . ':generateContent';
        
        $payload = [
            'contents' => [
                [
                    'parts' => [['text' => $prompt]]
                ]
            ]
        ];

        if ($systemMessage) {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemMessage]
                ]
            ];
        }

        $response = $this->client()->post($url, $payload);

        if ($response->failed()) {
            throw VectorSearchException::apiError('Gemini', $response->body(), $response->status());
        }

        $data = $response->json();
        
        return new AiResponse(
            $data['candidates'][0]['content']['parts'][0]['text'],
            [
                'candidates' => $data['candidates'] ?? [],
                'usage' => $data['usageMetadata'] ?? [],
            ]
        );
    }
}

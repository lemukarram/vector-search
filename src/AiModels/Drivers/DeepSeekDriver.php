<?php

namespace LeMukarram\VectorSearch\AiModels\Drivers;

use GuzzleHttp\Client;
use LeMukarram\VectorSearch\Contracts\AiChatDriver;
use LeMukarram\VectorSearch\Contracts\AiEmbeddingDriver;

class DeepSeekDriver implements AiChatDriver, AiEmbeddingDriver
{
    protected Client $client;
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => 'https://api.deepseek.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config['api_key'],
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => 30,
        ]);
    }

    public function embed(string $text): array
    {
        $response = $this->client->post('embeddings', [
            'json' => [
                'model' => $this->config['embedding_model'],
                'input' => $text,
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['data'][0]['embedding'];
    }

    public function chat(string $prompt, string $context): string
    {
        $response = $this->client->post('chat/completions', [
            'json' => [
                'model' => $this->config['chat_model'],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are a helpful assistant. Answer the user's question based ONLY on the following context:\n\nContext:\n{$context}"
                    ],
                    ['role' => 'user', 'content' => $prompt]
                ],
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['choices'][0]['message']['content'];
    }
}
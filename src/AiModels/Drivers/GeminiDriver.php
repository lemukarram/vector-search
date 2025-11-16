<?php

namespace LeMukarram\VectorSearch\AiModels\Drivers;

use GuzzleHttp\Client;
use LeMukarram\VectorSearch\Contracts\AiChatDriver;
use LeMukarram\VectorSearch\Contracts\AiEmbeddingDriver;

class GeminiDriver implements AiChatDriver, AiEmbeddingDriver
{
    protected Client $client;
    protected array $config;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => 30,
        ]);
    }

    public function embed(string $text): array
    {
        $url = $this->baseUrl . $this->config['embedding_model'] . ':embedContent?key=' . $this->config['api_key'];
        $response = $this->client->post($url, [
            'json' => [
                'content' => [
                    'parts' => [['text' => $text]]
                ]
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['embedding']['values'];
    }

    public function chat(string $prompt, string $context): string
    {
        $url = $this->baseUrl . $this->config['chat_model'] . ':generateContent?key=' . $this->config['api_key'];
        $fullPrompt = "You are a helpful assistant. Answer the user's question based ONLY on the following context:\n\nContext:\n{$context}\n\nUser Question:\n{$prompt}";

        $response = $this->client->post($url, [
            'json' => [
                'contents' => [
                    [
                        'parts' => [['text' => $fullPrompt]]
                    ]
                ]
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['candidates'][0]['content']['parts'][0]['text'];
    }
}
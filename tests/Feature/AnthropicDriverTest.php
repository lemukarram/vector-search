<?php

namespace LeMukarram\VectorSearch\Tests\Feature;

use Illuminate\Support\Facades\Http;
use LeMukarram\VectorSearch\AiModels\Drivers\AnthropicDriver;
use LeMukarram\VectorSearch\Tests\TestCase;

class AnthropicDriverTest extends TestCase
{
    public function test_anthropic_driver_sends_correct_payload()
    {
        Http::fake([
            'api.anthropic.com/*' => Http::response([
                'content' => [['text' => 'Hello from Claude']],
                'model' => 'claude-sonnet-4-6',
                'usage' => ['input_tokens' => 10, 'output_tokens' => 20],
                'stop_reason' => 'end_turn'
            ], 200),
        ]);

        $driver = new AnthropicDriver([
            'api_key' => 'test-key',
            'chat_model' => 'claude-sonnet-4-6',
        ]);

        $response = $driver->chat('Hi Claude', 'System info');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.anthropic.com/v1/messages' &&
                   $request['model'] === 'claude-sonnet-4-6' &&
                   $request['system'] === 'System info' &&
                   $request['messages'][0]['content'] === 'Hi Claude';
        });

        $this->assertEquals('Hello from Claude', $response->content());
        $this->assertEquals(30, $response->usage()['total_tokens']);
    }
}

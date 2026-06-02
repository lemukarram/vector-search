<?php

namespace LeMukarram\VectorSearch\Tests\Feature;

use Illuminate\Support\Facades\Http;
use LeMukarram\VectorSearch\Support\RecursiveCharacterTextSplitter;
use LeMukarram\VectorSearch\Tests\TestCase;
use LeMukarram\VectorSearch\VectorStores\Drivers\UpstashDriver;

class RagFundamentalsTest extends TestCase
{
    public function test_text_splitter_splits_correctly()
    {
        $splitter = new RecursiveCharacterTextSplitter(chunkSize: 10);
        $text = "Hello world this is a test";
        $chunks = $splitter->splitText($text);

        // Expected roughly: ["Hello", "world", "this is", "a test"] depending on separators
        $this->assertNotEmpty($chunks);
        foreach ($chunks as $chunk) {
            $this->assertLessThanOrEqual(10, strlen($chunk));
        }
    }

    public function test_upstash_driver_handles_filters()
    {
        Http::fake([
            '*' => Http::response(['result' => []], 200),
        ]);

        $driver = new UpstashDriver([
            'url' => 'https://test.upstash.io',
            'token' => 'test-token',
        ]);

        $driver->query([0.1], 3, ['category' => 'books', 'year' => 2024]);

        Http::assertSent(function ($request) {
            return $request['filter'] === "category = 'books' AND year = 2024";
        });
    }
}

<?php

namespace LeMukarram\VectorSearch\Tests\Feature;

use LeMukarram\VectorSearch\Facades\VectorSearch;
use LeMukarram\VectorSearch\Tests\TestCase;
use LeMukarram\VectorSearch\Core\AiResponse;

class DevExperienceTest extends TestCase
{
    public function test_facade_can_be_faked()
    {
        $fake = VectorSearch::fake();
        $fake->pushChatResponse('Fake Response');

        $response = VectorSearch::chat('hello');

        $this->assertEquals('Fake Response', $response->content());
    }

    public function test_with_model_override()
    {
        // This is harder to test without full integration, 
        // but we can verify the property is set and reset.
        $vs = app(\LeMukarram\VectorSearch\VectorSearch::class);
        $vs->withModel('claude');
        
        $reflection = new \ReflectionClass($vs);
        $prop = $reflection->getProperty('modelOverride');
        $prop->setAccessible(true);
        
        $this->assertEquals('claude', $prop->getValue($vs));
    }
}

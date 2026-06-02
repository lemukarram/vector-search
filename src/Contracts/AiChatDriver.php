<?php

namespace LeMukarram\VectorSearch\Contracts;

use LeMukarram\VectorSearch\Core\AiResponse;

interface AiChatDriver
{
    /**
     * Generate a chat response.
     */
    public function chat(string $prompt, string $systemMessage = ''): AiResponse;
}

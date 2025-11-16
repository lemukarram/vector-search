<?php

namespace LeMukarram\VectorSearch\Contracts;

interface AiChatDriver
{
    /**
     * Generate a chat response based on a prompt and context.
     *
     * @param string $prompt The user's question
     * @param string $context The retrieved documents
     * @return string The AI's answer
     */
    public function chat(string $prompt, string $context): string;
}
<?php

namespace LeMukarram\VectorSearch\Contracts;

interface AiEmbeddingDriver
{
    /**
     * Turn a single string of text into a vector (array of floats).
     *
     * @param string $text
     * @return array
     */
    public function embed(string $text): array;
}
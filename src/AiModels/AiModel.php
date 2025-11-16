<?php

namespace LeMukarram\VectorSearch\AiModels;

/**
 * A DTO for an AI Driver instance.
 */
class AiModel
{
    public function __construct(
        public readonly ?\LeMukarram\VectorSearch\Contracts\AiEmbeddingDriver $embeddingDriver = null,
        public readonly ?\LeMukarram\VectorSearch\Contracts\AiChatDriver $chatDriver = null
    ) {}
}
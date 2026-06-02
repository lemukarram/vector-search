<?php

namespace LeMukarram\VectorSearch\Core;

class AiResponse
{
    public function __construct(
        protected string $content,
        protected array $metadata = []
    ) {}

    public function content(): string
    {
        return $this->content;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function usage(): array
    {
        return $this->metadata['usage'] ?? [];
    }
}

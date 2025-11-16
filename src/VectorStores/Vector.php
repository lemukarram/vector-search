<?php

namespace LeMukarram\VectorSearch\VectorStores;

/**
 * A simple DTO (Data Transfer Object) for a vector.
 */
class Vector
{
    public function __construct(
        public readonly string $id,
        public readonly array $values,
        public readonly array $metadata = []
    ) {}

    /**
     * Get the vector as an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'values' => $this->values,
            'metadata' => $this->metadata,
        ];
    }
}
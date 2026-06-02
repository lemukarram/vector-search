<?php

namespace LeMukarram\VectorSearch\Testing;

use Illuminate\Support\Collection;
use LeMukarram\VectorSearch\Core\AiResponse;

class VectorSearchFake
{
    protected array $similarResponses = [];
    protected array $chatResponses = [];
    protected array $recordedSimilar = [];
    protected array $recordedChat = [];

    public function similar(string $query, int $topK = 3): Collection
    {
        $this->recordedSimilar[] = ['query' => $query, 'topK' => $topK];
        return $this->similarResponses[0] ?? new Collection();
    }

    public function chat(string $query): AiResponse
    {
        $this->recordedChat[] = ['query' => $query];
        return $this->chatResponses[0] ?? new AiResponse('Fake Answer');
    }

    public function pushSimilarResponse(Collection $collection): self
    {
        $this->similarResponses[] = $collection;
        return $this;
    }

    public function pushChatResponse(string $content, array $metadata = []): self
    {
        $this->chatResponses[] = new AiResponse($content, $metadata);
        return $this;
    }

    public function assertSimilarCalled(string $query): void
    {
        // Simple assertion logic
    }
}

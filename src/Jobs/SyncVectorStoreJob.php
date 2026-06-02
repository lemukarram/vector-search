<?php

namespace LeMukarram\VectorSearch\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LeMukarram\VectorSearch\Core\AiModelManager;
use LeMukarram\VectorSearch\Core\VectorStoreManager;
use LeMukarram\VectorSearch\VectorStores\Vector;

class SyncVectorStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $modelClass,
        protected mixed $modelId,
        protected string $text,
        protected string $vectorId,
        protected array $metadata = []
    ) {}

    public function handle(AiModelManager $ai, VectorStoreManager $store): void
    {
        // 1. Get embedding
        $embedding = $ai->embeddingDriver()->embed($this->text);

        // 2. Create Vector DTO
        $vector = new Vector(
            id: $this->vectorId,
            values: $embedding,
            metadata: array_merge([
                'model_class' => $this->modelClass,
                'model_id' => $this->modelId,
                'text' => $this->text,
            ], $this->metadata)
        );

        // 3. Upsert
        $store->store()->upsert([$vector]);
    }
}

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

class BatchSyncVectorStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected array $items // Array of ['text' => ..., 'id' => ..., 'metadata' => ...]
    ) {}

    public function handle(AiModelManager $ai, VectorStoreManager $store): void
    {
        $vectors = [];
        foreach ($this->items as $item) {
            $embedding = $ai->embeddingDriver()->embed($item['text']);
            $vectors[] = new Vector(
                id: $item['id'],
                values: $embedding,
                metadata: $item['metadata']
            );
        }

        $store->store()->upsert($vectors);
    }
}

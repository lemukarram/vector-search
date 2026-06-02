<?php

namespace LeMukarram\VectorSearch\Traits;

use Illuminate\Database\Eloquent\Model;
use LeMukarram\VectorSearch\Jobs\SyncVectorStoreJob;
use LeMukarram\VectorSearch\Jobs\DeleteVectorStoreJob;

trait VectorSearchable
{
    /**
     * Boot the trait.
     */
    public static function bootVectorSearchable(): void
    {
        static::saved(function (Model $model) {
            $model->syncToVectorStore();
        });

        static::deleted(function (Model $model) {
            $model->deleteFromVectorStore();
        });
    }

    public function syncToVectorStore(): void
    {
        $text = $this->getVectorText();
        if (empty($text)) return;

        $metadata = method_exists($this, 'getVectorMetadata') 
            ? $this->getVectorMetadata() 
            : [];

        $metadata = array_merge($metadata, [
            'model_class' => get_class($this),
            'model_id' => $this->getKey(),
        ]);

        // Handle Chunking
        $chunkSize = config('vector-search.rag.chunk_size', 1000);
        $chunkOverlap = config('vector-search.rag.chunk_overlap', 200);

        $splitter = new \LeMukarram\VectorSearch\Support\RecursiveCharacterTextSplitter($chunkSize, $chunkOverlap);
        $chunks = $splitter->splitText($text);

        foreach ($chunks as $index => $chunk) {
            SyncVectorStoreJob::dispatch(
                get_class($this),
                $this->getKey(),
                $chunk,
                $this->getVectorId() . ':chunk:' . $index,
                array_merge($metadata, ['chunk_index' => $index])
            );
        }
    }

    public function deleteFromVectorStore(): void
    {
        DeleteVectorStoreJob::dispatch($this->getVectorId());
        // ToDo: Handle deleting all chunks if chunking is used
    }

    /**
     * Get the text to be vectorized.
     * This is the helper that uses your getVectorColumns() idea.
     */
    public function getVectorText(): string
    {
        if (!method_exists($this, 'getVectorColumns')) {
            throw new \Exception(get_class($this) . ' must implement getVectorColumns()');
        }

        $text = [];
        foreach ($this->getVectorColumns() as $column) {
            $text[] = (string) $this->{$column};
        }
        return implode(" ", array_filter($text));
    }

    /**
     * Get the unique ID for this model in the vector store.
     * e.g., "App\Models\Post:123"
     */
    public function getVectorId(): string
    {
        return get_class($this) . ':' . $this->getKey();
    }
}
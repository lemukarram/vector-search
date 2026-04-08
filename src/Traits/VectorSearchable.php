<?php

namespace LeMukarram\VectorSearch\Traits;

use Illuminate\Database\Eloquent\Model;
use LeMukarram\VectorSearch\Jobs\SyncVectorStoreJob;
use LeMukarram\VectorSearch\Jobs\DeleteVectorStoreJob;

trait VectorSearchable
{
    /**
     * Boot the trait.
     * This is the "magic" that listens for Eloquent events.
     */
    public static function bootVectorSearchable(): void
    {
        static::saved(function (Model $model) {
            // 1. Get text
            $text = $model->getVectorText();
            if (empty($text)) return; // Don't sync empty data

            // Dispatch job to handle embedding and upsert
            SyncVectorStoreJob::dispatch(
                get_class($model),
                $model->getKey(),
                $text,
                $model->getVectorId()
            );
        });

        static::deleted(function (Model $model) {
            DeleteVectorStoreJob::dispatch($model->getVectorId());
        });
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
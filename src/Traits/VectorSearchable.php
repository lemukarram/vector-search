<?php

namespace LeMukarram\VectorSearch\Traits;

use Illuminate\Database\Eloquent\Model;
use LeMukarram\VectorSearch\Core\AiModelManager;
use LeMukarram\VectorSearch\Core\VectorStoreManager;
use LeMukarram\VectorSearch\VectorStores\Vector;

trait VectorSearchable
{
    /**
     * Boot the trait.
     * This is the "magic" that listens for Eloquent events.
     */
    public static function bootVectorSearchable(): void
    {
        static::saved(function (Model $model) {
            // Get managers from the service container
            $ai = app(AiModelManager::class);
            $store = app(VectorStoreManager::class);

            // 1. Get text
            $text = $model->getVectorText();
            if (empty($text)) return; // Don't sync empty data

            // 2. Get embedding
            $embedding = $ai->embeddingDriver()->embed($text);

            // 3. Create Vector DTO
            $vector = new Vector(
                id: $model->getVectorId(),
                values: $embedding,
                metadata: [
                    'model_class' => get_class($model),
                    'model_id' => $model->getKey(),
                    'text' => $text, // Store truncated text for debugging
                ]
            );

            // 4. Upsert
            $store->store()->upsert([$vector]);
        });

        static::deleted(function (Model $model) {
            $store = app(VectorStoreManager::class);
            $store->store()->delete([$model->getVectorId()]);
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

    /**
     * Your brilliant idea:
     * User *must* implement this in their model.
     *
     * @return array
     */
    // abstract public function getVectorColumns(): array;
    // We can't make it abstract in a Trait, so we'll just
    // check for its existence in getVectorText().
}
<?php

namespace LeMukarram\VectorSearch;

use Illuminate\Database\Eloquent\Collection;
use LeMukarram\VectorSearch\Core\AiModelManager;
use LeMukarram\VectorSearch\Core\VectorStoreManager;

/**
 * This is the main class that the Facade points to.
 * It's the "orchestrator" that uses the managers.
 */
class VectorSearch
{
    public function __construct(
        protected AiModelManager $ai,
        protected VectorStoreManager $store
    ) {}

    /**
     * Get the VectorStoreManager instance.
     */
    public function store(): VectorStoreManager
    {
        return $this->store;
    }

    /**
     * Get the AiModelManager instance.
     */
    public function model(): AiModelManager
    {
        return $this->ai;
    }

    /**
     * Find the most similar Eloquent models for a query.
     */
    public function similar(string $query, int $topK = 3): Collection
    {
        // 1. Get embedding for the query
        $vector = $this->ai->embeddingDriver()->embed($query);

        // 2. Query the vector database
        $results = $this->store->store()->query($vector, $topK);

        // 3. Hydrate models
        return $this->hydrateModels($results);
    }

    /**
     * Get a direct chat response using RAG.
     */
    public function chat(string $query): string
    {
        // 1. Get relevant models
        $models = $this->similar($query, 3);

        // 2. Build the context string
        $context = $this->buildContextFromModels($models);
        if (empty($context)) {
            $context = "No relevant context found.";
        }

        // 3. Ask the AI
        return $this->ai->chatDriver()->chat($query, $context);
    }

    /**
     * Turn vector DB results into an Eloquent Collection.
     */
    protected function hydrateModels(array $results): Collection
    {
        $models = new Collection();
        $modelsById = [];

        // Group by model class to query efficiently
        foreach ($results as $result) {
            $metadata = $result['metadata'];
            if (isset($metadata['model_class']) && isset($metadata['model_id'])) {
                $modelsById[$metadata['model_class']][] = $metadata['model_id'];
            }
        }

        // Eager load models
        foreach ($modelsById as $class => $ids) {
            if (class_exists($class)) {
                $models = $models->merge($class::findMany($ids));
            }
        }

        return $models;
    }

    /**
     * Build a text context from a Collection of models.
     */
    protected function buildContextFromModels(Collection $models): string
    {
        $context = "";
        foreach ($models as $model) {
            if (method_exists($model, 'getVectorColumns')) {
                $cols = $model->getVectorColumns();
                $text = [];
                foreach ($cols as $col) {
                    $text[] = "{$col}: " . $model->{$col};
                }
                $context .= "--- Document (from {$model->getTable()}:{$model->getKey()}) ---\n" . implode("\n", $text) . "\n\n";
            }
        }
        return trim($context);
    }
}
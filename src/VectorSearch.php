<?php

namespace LeMukarram\VectorSearch;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use LeMukarram\VectorSearch\Core\AiModelManager;
use LeMukarram\VectorSearch\Core\AiResponse;
use LeMukarram\VectorSearch\Core\VectorStoreManager;

/**
 * This is the main class that the Facade points to.
 * It's the "orchestrator" that uses the managers.
 */
class VectorSearch
{
    protected array $filters = [];
    protected ?string $modelOverride = null;
    protected ?string $storeOverride = null;

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
     * Add a metadata filter to the next query.
     */
    public function whereMetadata(string $key, mixed $value): self
    {
        $this->filters[$key] = $value;
        return $this;
    }

    /**
     * Override the default AI model for the next request.
     */
    public function withModel(string $name): self
    {
        $this->modelOverride = $name;
        return $this;
    }

    /**
     * Override the default vector store for the next request.
     */
    public function withStore(string $name): self
    {
        $this->storeOverride = $name;
        return $this;
    }

    /**
     * Use LLM to expand a single query into multiple variations for better retrieval.
     */
    public function multiQuery(string $query, int $count = 3): Collection
    {
        $prompt = "Generate {$count} different variations of the following user query to help find more relevant documents in a vector database. Output only the variations, one per line.\n\nQuery: {$query}";
        
        $response = $this->ai->chatDriver()->chat($prompt, "You are a search expert.");
        $variations = array_filter(explode("\n", $response->content()));
        
        $allResults = collect();
        foreach (array_merge([$query], $variations) as $q) {
            $allResults = $allResults->merge($this->similar($q, 3));
        }

        return $allResults->unique(fn($m) => get_class($m) . ':' . $m->getKey());
    }

    /**
     * Perform Hybrid Search (Vector + Full-text).
     */
    public function hybrid(string $query, int $topK = 5): Collection
    {
        // This is a placeholder for a more complex RRF implementation.
        // Modern vector stores like Pinecone/Upstash support this natively via query parameters.
        return $this->similar($query, $topK);
    }
    public function similar(string $query, int $topK = 3): Collection
    {
        $ttl = config('vector-search.cache_ttl');
        $cacheKey = 'vector_search_embed_' . md5($query);

        // 1. Get embedding for the query (Cached)
        $vector = $ttl 
            ? Cache::remember($cacheKey, $ttl, fn () => $this->ai->embeddingDriver($this->modelOverride)->embed($query))
            : $this->ai->embeddingDriver($this->modelOverride)->embed($query);

        // 2. Query the vector database
        $results = $this->store->store($this->storeOverride)->query($vector, $topK, $this->filters);

        // Reset state after query
        $this->filters = [];
        $this->modelOverride = null;
        $this->storeOverride = null;

        // 3. Hydrate models
        return $this->hydrateModels($results);
    }

    /**
     * Get a direct chat response using RAG.
     */
    public function chat(string $query): AiResponse
    {
        $ttl = config('vector-search.cache_ttl');
        $cacheKey = 'vector_search_chat_' . md5($query);

        if ($ttl && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // 1. Get relevant models
        $models = $this->similar($query, 3);

        // 2. Build the context string
        $context = $this->buildContextFromModels($models);
        
        $systemPrompt = config('vector-search.rag.system_prompt');
        
        if (empty($context)) {
            $context = config('vector-search.rag.no_context_message', 'No relevant context found.');
        }

        // 3. Prepare the prompt (Replace placeholders)
        $finalPrompt = str_replace(
            ['{{context}}', '{{query}}'],
            [$context, $query],
            $systemPrompt
        );

        // 4. Ask the AI
        $response = $this->ai->chatDriver($this->modelOverride)->chat($query, $finalPrompt);

        if ($ttl) {
            Cache::put($cacheKey, $response, $ttl);
        }

        // Reset state
        $this->modelOverride = null;

        return $response;
    }

    /**
     * Turn vector DB results into an Eloquent Collection, preserving relevance order.
     */
    protected function hydrateModels(array $results): Collection
    {
        $orderedIds = [];
        $modelsByClass = [];

        foreach ($results as $result) {
            $metadata = $result['metadata'];
            if (isset($metadata['model_class']) && isset($metadata['model_id'])) {
                $class = $metadata['model_class'];
                $id = $metadata['model_id'];
                
                $orderedIds[] = ['class' => $class, 'id' => $id];
                $modelsByClass[$class][] = $id;
            }
        }

        $fetchedModels = [];
        foreach ($modelsByClass as $class => $ids) {
            if (class_exists($class)) {
                // Use findMany and key by ID for fast lookup
                $models = $class::findMany($ids)->keyBy(fn($m) => $m->getKey());
                $fetchedModels[$class] = $models;
            }
        }

        $collection = new Collection();
        foreach ($orderedIds as $item) {
            $class = $item['class'];
            $id = $item['id'];
            
            if (isset($fetchedModels[$class][$id])) {
                $collection->push($fetchedModels[$class][$id]);
            }
        }

        return $collection->unique(fn($m) => get_class($m) . ':' . $m->getKey());
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
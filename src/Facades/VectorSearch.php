<?php

namespace LeMukarram\VectorSearch\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Eloquent\Collection similar(string $query, int $topK = 3)
 * @method static \LeMukarram\VectorSearch\Core\AiResponse chat(string $query)
 * @method static \LeMukarram\VectorSearch\Core\VectorStoreManager store()
 * @method static \LeMukarram\VectorSearch\Core\AiModelManager model()
 * @method static \LeMukarram\VectorSearch\VectorSearch whereMetadata(string $key, mixed $value)
 *
 * @see \LeMukarram\VectorSearch\VectorSearch
 */
class VectorSearch extends Facade
{
    /**
     * Replace the bound instance with a fake.
     */
    public static function fake()
    {
        static::swap($fake = new \LeMukarram\VectorSearch\Testing\VectorSearchFake());
        return $fake;
    }

    protected static function getFacadeAccessor()
    {
        // This 'vector-search' string must match our singleton binding
        // in the Service Provider.
        return 'vector-search';
    }
}
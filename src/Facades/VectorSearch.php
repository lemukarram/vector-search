<?php

namespace LeMukarram\VectorSearch\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Eloquent\Collection similar(string $query, int $topK = 3)
 * @method static string chat(string $query)
 * @method static \LeMukarram\VectorSearch\Core\VectorStoreManager store()
 * @method static \LeMukarram\VectorSearch\Core\AiModelManager model()
 *
 * @see \LeMukarram\VectorSearch\VectorSearch
 */
class VectorSearch extends Facade
{
    protected static function getFacadeAccessor()
    {
        // This 'vector-search' string must match our singleton binding
        // in the Service Provider.
        return 'vector-search';
    }
}
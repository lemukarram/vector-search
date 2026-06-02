<?php

namespace LeMukarram\VectorSearch\Tests\Feature;

use Illuminate\Database\Eloquent\Collection;
use LeMukarram\VectorSearch\Tests\TestCase;
use Mockery;

class SearchRetrievalTest extends TestCase
{
    public function test_hydrate_models_preserves_order()
    {
        $vs = app(\LeMukarram\VectorSearch\VectorSearch::class);
        
        $results = [
            ['metadata' => ['model_class' => MockModel::class, 'model_id' => 2]],
            ['metadata' => ['model_class' => MockModel::class, 'model_id' => 1]],
            ['metadata' => ['model_class' => MockModel::class, 'model_id' => 3]],
        ];

        $reflection = new \ReflectionClass($vs);
        $method = $reflection->getMethod('hydrateModels');
        $method->setAccessible(true);

        $collection = $method->invoke($vs, $results);

        $this->assertCount(3, $collection);
        $this->assertEquals(2, $collection[0]->id);
        $this->assertEquals(1, $collection[1]->id);
        $this->assertEquals(3, $collection[2]->id);
    }
}

class MockModel extends \Illuminate\Database\Eloquent\Model {
    protected $guarded = [];
    public static function findMany($ids) {
        $collection = new Collection();
        foreach ($ids as $id) {
            $m = new self(['id' => $id]);
            $m->exists = true;
            $collection->push($m);
        }
        return $collection;
    }
    public function getKey() { return $this->id; }
}

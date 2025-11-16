<?php

namespace LeMukarram\VectorSearch\Contracts;

use LeMukarram\VectorSearch\VectorStores\Vector;

interface VectorStoreDriver
{
    /**
     * Add or update a batch of vectors.
     *
     * @param array $vectors Array of LeMukarram\VectorSearch\VectorStores\Vector objects
     * @return bool
     */
    public function upsert(array $vectors): bool;

    /**
     * Find the most similar vectors.
     *
     * @param array $vector The vector to search for
     * @param int $topK The number of results to return
     * @param array $filter Optional metadata filter
     * @return array
     */
    public function query(array $vector, int $topK, array $filter = []): array;

    /**
     * Delete vectors by their IDs.
     *
     * @param array $ids
     * @return bool
     */
    public function delete(array $ids): bool;
}
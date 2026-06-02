<?php

namespace LeMukarram\VectorSearch\Support;

class ReciprocalRankFusion
{
    /**
     * Merge multiple ranked lists using RRF.
     * Each list is an array of items where the item is the key (e.g., "Model:ID").
     */
    public static function fuse(array $rankings, int $k = 60): array
    {
        $scores = [];
        
        foreach ($rankings as $list) {
            foreach ($list as $rank => $item) {
                if (!isset($scores[$item])) {
                    $scores[$item] = 0;
                }
                // RRF formula: 1 / (k + rank)
                $scores[$item] += 1 / ($k + $rank + 1);
            }
        }

        arsort($scores);
        return array_keys($scores);
    }
}

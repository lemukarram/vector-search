<?php

namespace LeMukarram\VectorSearch\Support;

class RecursiveCharacterTextSplitter
{
    public function __construct(
        protected int $chunkSize = 1000,
        protected int $chunkOverlap = 200,
        protected array $separators = ["\n\n", "\n", " ", ""]
    ) {}

    public function splitText(string $text): array
    {
        return $this->recursiveSplit($text, $this->separators);
    }

    protected function recursiveSplit(string $text, array $separators): array
    {
        $finalChunks = [];
        
        // Get the appropriate separator
        $separator = $separators[count($separators) - 1];
        $newSeparators = [];
        
        foreach ($separators as $i => $s) {
            if ($s === "") {
                $separator = $s;
                break;
            }
            if (str_contains($text, $s)) {
                $separator = $s;
                $newSeparators = array_slice($separators, $i + 1);
                break;
            }
        }

        // Split by the separator
        if ($separator !== "") {
            $splits = explode($separator, $text);
        } else {
            $splits = str_split($text);
        }

        $goodSplits = [];
        foreach ($splits as $s) {
            if ($separator !== "") {
                $s = trim($s);
            }
            if ($s !== "") {
                $goodSplits[] = $s;
            }
        }

        // Combine into chunks
        $currentChunk = "";
        foreach ($goodSplits as $split) {
            if (strlen($currentChunk) + strlen($split) + strlen($separator) <= $this->chunkSize) {
                $currentChunk .= ($currentChunk === "" ? "" : $separator) . $split;
            } else {
                if ($currentChunk !== "") {
                    $finalChunks[] = $currentChunk;
                }
                
                if (strlen($split) > $this->chunkSize) {
                    // Split the long piece recursively
                    $recursiveChunks = $this->recursiveSplit($split, $newSeparators);
                    foreach ($recursiveChunks as $rc) {
                        $finalChunks[] = $rc;
                    }
                    $currentChunk = "";
                } else {
                    $currentChunk = $split;
                }
            }
        }

        if ($currentChunk !== "") {
            $finalChunks[] = $currentChunk;
        }

        return $this->mergeWithOverlap($finalChunks);
    }

    protected function mergeWithOverlap(array $chunks): array
    {
        if ($this->chunkOverlap <= 0 || count($chunks) <= 1) {
            return $chunks;
        }

        $merged = [];
        // A simpler implementation for now: 
        // In a real splitter, we'd handle overlap during the split process.
        // For simplicity, let's just return the chunks.
        return $chunks;
    }
}

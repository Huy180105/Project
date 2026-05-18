<?php

namespace App\Services;

class TextChunker
{
    public function chunk(string $text, int $maxLength = 1200, int $overlap = 150): array
    {
        $normalized = trim(preg_replace('/\s+/', ' ', $text) ?: '');

        if ($normalized === '') {
            return [];
        }

        $chunks = [];
        $offset = 0;
        $length = strlen($normalized);

        while ($offset < $length) {
            $chunk = substr($normalized, $offset, $maxLength);
            $chunks[] = trim($chunk);

            if ($offset + $maxLength >= $length) {
                break;
            }

            $offset += max(1, $maxLength - $overlap);
        }

        return array_values(array_filter($chunks));
    }
}


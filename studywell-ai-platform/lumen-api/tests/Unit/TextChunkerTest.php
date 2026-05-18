<?php

namespace Tests\Unit;

use App\Services\TextChunker;
use PHPUnit\Framework\TestCase;

class TextChunkerTest extends TestCase
{
    public function test_it_chunks_long_text(): void
    {
        $chunks = (new TextChunker())->chunk(str_repeat('study focus data ', 300), 200, 40);

        $this->assertGreaterThan(1, count($chunks));
        $this->assertLessThanOrEqual(200, strlen($chunks[0]));
    }
}

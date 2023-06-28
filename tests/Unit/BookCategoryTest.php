<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\BookCategory;

class BookCategoryTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $user = factory(BookCategory::class)->create([
            'name' => 'Fiction',
            'sequence' => '1',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

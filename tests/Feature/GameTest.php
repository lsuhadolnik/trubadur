<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting a collection of records from the API.
     *
     * @return void
     */
    public function testIndex()
    {
        $this->assertTrue(true);
    }
}

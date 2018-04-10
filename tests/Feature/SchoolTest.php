<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Difficulty;
use App\Grade;
use App\School;

class SchoolTest extends TestCase
{
    use RefreshDatabase;

    const SCHOOL_COUNT = 5;
    const GRADE_COUNT = 5;

    /**
     * Test getting a paginated collection of records from the API.
     *
     * @return void
     */
    public function testIndexPaginated()
    {
        $collection = $this->createCollection();

        $response = $this->getJson("api/schools");
        $response->assertStatus(200);
        $data = $response->json()['data'];

        $this->validateCollection($collection, $data);
    }

    /**
     * Test getting a collection of records from the API.
     *
     * @return void
     */
    public function testIndex()
    {
        $collection = $this->createCollection();

        $response = $this->getJson("api/schools?per_page=0");
        $response->assertStatus(200);
        $data = $response->json();

        $this->validateCollection($collection, $data);
    }

    private function createCollection() {
        $schools = factory(School::class, self::SCHOOL_COUNT)->create()->each(function ($school) {
            factory(Grade::class, self::GRADE_COUNT)->create()->each(function ($grade) use ($school) {
                $difficulty = factory(Difficulty::class)->create();
                $school->grades()->attach($grade->id, ['difficulty_id' => $difficulty->id]);
            });
        });

        return $schools;
    }

    private function validateCollection($collection, $data) {
        $this->assertCount(self::SCHOOL_COUNT, $data);

        foreach ($collection as $record) {
            foreach ($data as $item) {
                if ($item['id'] === $record->id) {
                    $this->validateRecord($record, $item);
                    break;
                }
            }
        }
    }

    /**
     * Test getting a record from the API.
     *
     * @return void
     */
    public function testShow()
    {
        $record = $this->createRecord();

        $response = $this->getJson("api/schools/{$record->id}");
        $response->assertStatus(200);
        $item = $response->json();

        $this->validateRecord($record, $item);
    }

    private function createRecord() {
        $school = factory(School::class)->create();
        factory(Grade::class, self::GRADE_COUNT)->create()->each(function ($grade) use ($school) {
            $difficulty = factory(Difficulty::class)->create();
            $school->grades()->attach($grade->id, ['difficulty_id' => $difficulty->id]);
        });

        return $school;
    }

    private function validateRecord($record, $item) {
        $this->assertEquals($record->id, $item['id']);
        $this->assertEquals($record->name, $item['name']);
        $this->assertEquals($record->type, $item['type']);
        $this->assertEquals($record->country_id, $item['country_id']);
        $this->assertCount(self::GRADE_COUNT, $item['grades']);
    }
}

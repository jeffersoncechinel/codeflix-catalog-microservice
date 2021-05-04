<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        // creates 1 random genre
        factory(Genre::class, 1)->create();
        $categories = Genre::all();
        $this->assertCount(1, $categories);

        $genreKeys = array_keys($categories->first()->getAttributes());

        // normalize arrays then compare
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at',
        ], $genreKeys);
    }

    public function testCreate()
    {
        $genre = Genre::create([
            'name' => 'Genre1',
        ]);
        // updates the model's instance with the new data
        $genre->refresh();

        $this->assertEquals('Genre1', $genre->name);
        $this->assertTrue($genre->is_active);
        $this->assertTrue(Uuid::isValid($genre->id));

        $genre = Genre::create([
            'name' => 'Genre1',
            'is_active' => false,
        ]);
        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'Genre1',
            'is_active' => true,
        ]);
        $this->assertTrue($genre->is_active);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => false,
        ]);

        $data = [
            'name' => 'Test Name Updated',
            'is_active' => true,
        ];

        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        $genre = factory(Genre::class, 1)->create()->first();
        $genre->delete();
        $this->assertNotNull($genre->deleted_at);
    }
}

<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Lang;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.index'));

        $response->assertStatus(200)
            ->assertJson([$genre->toArray()]);
    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response->assertStatus(200)
            ->assertJson($genre->toArray());
    }

    public function testInvalidationData()
    {
        // 1
        $response = $this->json('POST', route('genres.store'), []);
        $this->assertInvalidationRequired($response);

        // 2
        $response = $this->json('POST', route('genres.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a',
        ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        // 3
        $genre = factory(Genre::class)->create();
        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id])
        );
        $this->assertInvalidationRequired($response);

        // 4
        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id]),
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a',
            ]
        );

        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);
    }

    protected function assertInvalidationRequired(TestResponse $response)
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                Lang::get('validation.required', ['attribute' => 'name']),
            ]);
    }

    protected function assertInvalidationMax(TestResponse $response)
    {
        $response->assertStatus(422)
            ->assertJsonFragment([
                Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255]),
            ]);
    }

    protected function assertInvalidationBoolean(TestResponse $response)
    {
        $response->assertStatus(422)
            ->assertJsonMissingValidationErrors(['is active'])
            ->assertJsonFragment([
                Lang::get('validation.boolean', ['attribute' => 'is active']),
            ]);
    }

    public function testStore()
    {
        // 1
        $response = $this->json('POST', route('genres.store'), [
            'name' => 'test',
        ]);

        $genre = Genre::find($response->json('id'));
        $response->assertStatus(201)->assertJson($genre->toArray());

        $this->assertTrue($response->json('is_active'));

        // 2
        $response = $this->json('POST', route('genres.store'), [
            'name' => 'test',
            'is_active' => false,
        ]);

        $response->assertJsonFragment([
            'is_active' => false,
        ]);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => false,
        ]);

        // 1
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [
            'name' => 'test',
            'is_active' => true,
        ]);

        $genre = Genre::find($response->json('id'));

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'is_active' => true,
            ]);
    }

    public function testDestroy()
    {
        $category = factory(Category::class)->create();
        $response = $this->json('DELETE', route('categories.destroy', ['category' => $category->id]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($category->id));
        $this->assertNotNull(Category::withTrashed()->find($category->id));
    }
}

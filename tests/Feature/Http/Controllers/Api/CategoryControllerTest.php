<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Lang;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function testInvalidationData()
    {
        // 1
        $response = $this->json('POST', route('categories.store'), []);
        $this->assertInvalidationRequired($response);

        // 2
        $response = $this->json('POST', route('categories.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a',
        ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        // 3
        $category = factory(Category::class)->create();
        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id])
        );
        $this->assertInvalidationRequired($response);

        // 4
        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
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
        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test',
        ]);

        $category = Category::find($response->json('id'));
        $response->assertStatus(201)->assertJson($category->toArray());

        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        // 2
        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test',
            'is_active' => false,
            'description' => 'lala',
        ]);

        $response->assertJsonFragment([
            'is_active' => false,
            'description' => 'lala',
        ]);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'description' => 'description',
            'is_active' => false,
        ]);

        // 1
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => 'test',
            'is_active' => true,
            'description' => 'description modified',
        ]);

        $category = Category::find($response->json('id'));

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'description' => 'description modified',
                'is_active' => true,
            ]);

        // 2 - Laravel has a middleware in controller that always converts '' to null
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => 'test',
            'description' => '',
        ]);

        $response->assertJsonFragment([
            'description' => null,
        ]);


        $category->description = 'test';
        $category->save();

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => 'test',
            'description' => null,
        ]);

        $response->assertJsonFragment([
            'description' => null,
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

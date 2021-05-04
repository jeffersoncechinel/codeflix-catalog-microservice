<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        // creates 1 random category
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);

        $categoryKeys = array_keys($categories->first()->getAttributes());

        // normalize arrays then compare
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'description',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at',
        ], $categoryKeys);
    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'Category1',
        ]);
        // updates the model's instance with the new data
        $category->refresh();

        $this->assertEquals('Category1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
        $this->assertTrue(Uuid::isValid($category->id));

        $category = Category::create([
            'name' => 'Category1',
            'description' => null,
        ]);
        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'Category1',
            'description' => 'Test Decription',
        ]);
        $this->assertEquals('Test Decription', $category->description);

        $category = Category::create([
            'name' => 'Category1',
            'is_active' => false,
        ]);
        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'Category1',
            'is_active' => true,
        ]);
        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'description' => 'Test Description',
            'is_active' => false,
        ]);

        $data = [
            'name' => 'Test Name Updated',
            'description' => 'Test Description Updated',
            'is_active' => true,
        ];

        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        $category = factory(Category::class, 1)->create()->first();
        $category->delete();
        $this->assertNotNull($category->deleted_at);
    }
}

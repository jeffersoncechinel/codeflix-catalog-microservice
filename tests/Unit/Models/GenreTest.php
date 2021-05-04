<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class GenreTest extends TestCase
{
    private $genre;

    /**
     * @test
     * @return void
     */
    public function testFillableAttribute()
    {
        $fillable = ['name', 'is_active'];
        $this->assertEquals($fillable, $this->genre->getFillable());
    }

    /**
     * @test
     * @return void
     */
    public function testUseTraits()
    {
        $traits = [SoftDeletes::class, Uuid::class];

        $genreTraits = array_keys(class_uses(Genre::class));
        $this->assertEquals($traits, $genreTraits);
    }

    /**
     * @test
     * @return void
     */
    public function testCastsAttribute()
    {
        $cast = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals($cast, $this->genre->getCasts());
    }

    /**
     * @test
     * @return void
     */
    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->genre->incrementing);
    }

    /**
     * @test
     * @return void
     */
    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $modelDates = array_values($this->genre->getDates());
        $this->assertEquals($dates, $modelDates);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = new Genre();
    }
}

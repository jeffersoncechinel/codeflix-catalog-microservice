<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function testFillableAttribute()
    {
        $fillable = ['name', 'description', 'is_active'];

        $model = new Category();
        $this->assertEquals($fillable, $model->getFillable());
    }

    /**
     * @test
     * @return void
     */
    public function testUseTraits()
    {
        $traits = [SoftDeletes::class, Uuid::class];

        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits, $categoryTraits);
    }

    /**
     * @test
     * @return void
     */
    public function testCastsAttribute()
    {
        $cast = ['id' => 'string'];

        $model = new Category();
        $this->assertEquals($cast, $model->getCasts());
    }

    /**
     * @test
     * @return void
     */
    public function testIncrementingAttribute()
    {
        $model = new Category();
        $this->assertFalse($model->incrementing);
    }

    /**
     * @test
     * @return void
     */
    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];

        $model = new Category();

        $modelDates = array_values($model->getDates());

        $this->assertEquals($dates, $modelDates);
    }
}

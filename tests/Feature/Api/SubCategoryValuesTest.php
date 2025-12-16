<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Value;
use App\Models\SubCategory;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubCategoryValuesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email' => 'admin@admin.com']);

        Sanctum::actingAs($user, [], 'web');

        $this->seed(\Database\Seeders\PermissionsSeeder::class);

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_gets_sub_category_values(): void
    {
        $subCategory = SubCategory::factory()->create();
        $values = Value::factory()
            ->count(2)
            ->create([
                'sub_category_id' => $subCategory->id,
            ]);

        $response = $this->getJson(
            route('api.sub-categories.values.index', $subCategory)
        );

        $response->assertOk()->assertSee($values[0]->nilai);
    }

    /**
     * @test
     */
    public function it_stores_the_sub_category_values(): void
    {
        $subCategory = SubCategory::factory()->create();
        $data = Value::factory()
            ->make([
                'sub_category_id' => $subCategory->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.sub-categories.values.store', $subCategory),
            $data
        );

        unset($data['user_id']);
        unset($data['sub_category_id']);
        unset($data['nilai']);
        unset($data['tahun']);

        $this->assertDatabaseHas('values', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $value = Value::latest('id')->first();

        $this->assertEquals($subCategory->id, $value->sub_category_id);
    }
}

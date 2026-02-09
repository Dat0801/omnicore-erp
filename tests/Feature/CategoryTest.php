<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_categories(): void
    {
        $user = User::factory()->admin()->create();
        ProductCategory::create([
            'name' => 'Electronics',
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Category/Index')
            ->has('categories.data')
            ->where('filters.search', '')
        );
    }

    public function test_admin_can_view_create_category_page(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)
            ->get(route('admin.categories.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Category/Create')
            ->has('parentCategories')
        );
    }
}

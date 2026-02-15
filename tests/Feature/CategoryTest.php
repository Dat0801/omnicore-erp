<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_categories(): void
    {
        $user = User::factory()->admin()->create();
        ProductCategory::factory()->create([
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

    public function test_admin_can_create_category(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)
            ->post(route('admin.categories.store'), [
                'name' => 'New Category',
                'description' => 'Test Description',
                'is_active' => true,
                'display_order' => 1,
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('product_categories', [
            'name' => 'New Category',
            'slug' => 'new-category',
            'description' => 'Test Description',
        ]);
    }

    public function test_admin_can_view_edit_category_page(): void
    {
        $user = User::factory()->admin()->create();
        $category = ProductCategory::create([
            'name' => 'Edit Me',
            'slug' => 'edit-me',
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.categories.edit', $category));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Category/Edit')
            ->has('category')
            ->where('category.id', $category->id)
            ->has('parentCategories')
        );
    }

    public function test_admin_can_update_category(): void
    {
        $user = User::factory()->admin()->create();
        $category = ProductCategory::create([
            'name' => 'Old Name',
            'slug' => 'old-name',
        ]);

        $response = $this->actingAs($user)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Updated Name',
                'description' => 'Updated Description',
                'is_active' => false,
                'display_order' => 2,
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('product_categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'slug' => 'updated-name',
            'description' => 'Updated Description',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_category(): void
    {
        $user = User::factory()->admin()->create();
        $category = ProductCategory::create([
            'name' => 'Delete Me',
            'slug' => 'delete-me',
        ]);

        $response = $this->actingAs($user)
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('product_categories', [
            'id' => $category->id,
        ]);
    }

    public function test_cannot_delete_category_with_children(): void
    {
        $user = User::factory()->admin()->create();
        $parent = ProductCategory::create([
            'name' => 'Parent',
            'slug' => 'parent',
        ]);
        ProductCategory::create([
            'name' => 'Child',
            'slug' => 'child',
            'parent_id' => $parent->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('admin.categories.edit', $parent))
            ->delete(route('admin.categories.destroy', $parent));

        $response->assertRedirect(route('admin.categories.edit', $parent));
        $response->assertSessionHas('error', 'Cannot delete category with sub-categories.');
        $this->assertDatabaseHas('product_categories', [
            'id' => $parent->id,
        ]);
    }

    public function test_admin_can_create_category_with_icon(): void
    {
        Storage::fake('public');
        $user = User::factory()->admin()->create();
        $file = UploadedFile::fake()->image('icon.jpg');

        $response = $this->actingAs($user)
            ->post(route('admin.categories.store'), [
                'name' => 'Category with Icon',
                'icon' => $file,
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('product_categories', [
            'name' => 'Category with Icon',
        ]);

        $category = ProductCategory::where('name', 'Category with Icon')->first();
        $this->assertNotNull($category->icon);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $category->icon));
    }

    public function test_icon_is_deleted_when_category_is_updated(): void
    {
        Storage::fake('public');
        $user = User::factory()->admin()->create();
        $oldFile = UploadedFile::fake()->image('old.jpg');
        $newFile = UploadedFile::fake()->image('new.jpg');

        // Create initial category with icon
        $this->actingAs($user)->post(route('admin.categories.store'), [
            'name' => 'Icon Update Test',
            'icon' => $oldFile,
        ]);

        $category = ProductCategory::where('name', 'Icon Update Test')->first();
        $oldIconPath = str_replace('/storage/', '', $category->icon);

        // Update with new icon
        $this->actingAs($user)->put(route('admin.categories.update', $category), [
            'name' => 'Icon Update Test',
            'icon' => $newFile,
        ]);

        $category->refresh();
        $newIconPath = str_replace('/storage/', '', $category->icon);

        Storage::disk('public')->assertMissing($oldIconPath);
        Storage::disk('public')->assertExists($newIconPath);
    }
}

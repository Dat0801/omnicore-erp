<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('slug')->unique()->after('name');
            $table->string('icon')->nullable()->after('description');
            $table->integer('display_order')->default(0)->after('icon');
            $table->boolean('is_active')->default(true)->after('display_order');
            $table->boolean('is_featured')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn(['description', 'slug', 'icon', 'display_order', 'is_active', 'is_featured']);
        });
    }
};

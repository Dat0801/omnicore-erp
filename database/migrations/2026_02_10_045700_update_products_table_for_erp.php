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
        if (Schema::hasColumn('products', 'code')) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('code', 'sku');
            });
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->after('sku');
            }
            if (! Schema::hasColumn('products', 'type')) {
                $table->enum('type', ['physical', 'service', 'digital'])->default('physical')->after('name');
            }
            if (! Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->nullable()->after('price');
            }
            if (! Schema::hasColumn('products', 'unit')) {
                $table->string('unit')->default('pcs')->after('price');
            }
            if (! Schema::hasColumn('products', 'weight')) {
                $table->decimal('weight', 8, 2)->nullable()->after('unit'); // in kg
            }
            if (! Schema::hasColumn('products', 'dimensions')) {
                $table->json('dimensions')->nullable()->after('weight'); // {l, w, h} in cm
            }
            if (! Schema::hasColumn('products', 'allow_backorders')) {
                $table->boolean('allow_backorders')->default(false)->after('is_active');
            }
            if (! Schema::hasColumn('products', 'tags')) {
                $table->json('tags')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('sku', 'code');
            $table->dropColumn([
                'barcode',
                'type',
                'cost_price',
                'unit',
                'weight',
                'dimensions',
                'allow_backorders',
                'tags',
            ]);
        });
    }
};

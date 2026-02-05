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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending');
            $table->string('source'); // 'webstore' or 'pos'
            $table->string('source_id'); // external ID/UUID
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->foreignId('warehouse_id')->constrained()->onDelete('restrict');
            $table->timestamps();

            $table->unique(['source', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

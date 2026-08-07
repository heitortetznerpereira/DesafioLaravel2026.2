<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("sales", function (Blueprint $table) {
            $table->id();

            $table->foreignId("product_id")->constrained()->cascadeOnDelete();

            $table
                ->foreignId("buyer_id")
                ->constrained("users")
                ->cascadeOnDelete();

            $table
                ->foreignId("seller_id")
                ->constrained("users")
                ->cascadeOnDelete();

            $table->decimal("price", 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("sales");
    }
};

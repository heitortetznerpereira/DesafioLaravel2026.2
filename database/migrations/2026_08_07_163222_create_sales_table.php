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

            $table
                ->foreignId("product_id")
                ->nullable()
                ->constrained("products")
                ->nullOnDelete();

            $table
                ->foreignId("buyer_id")
                ->constrained("users")
                ->cascadeOnDelete();

            $table
                ->foreignId("seller_id")
                ->nullable()
                ->constrained("users")
                ->nullOnDelete();

            $table->string("status")->default("pending");
            $table->string("pagbank_checkout_id")->nullable()->unique();

            $table->string("image")->nullable();
            $table->string("name");
            $table->integer("amount");
            $table->decimal("unit_price", 10, 2);

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

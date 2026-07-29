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
        Schema::table('users', function (Blueprint $table) {
           $table->boolean('is_admin');
           $table->string('phone_number');
           $table->string('cpf');
           $table->date('birth_date');
           $table->string('photo_path');
           $table->decimal('balance', 10, 2);
           $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
            $table->dropColumn('phone_number');
            $table->dropColumn('cpf');
            $table->dropColumn('birth_date');
            $table->dropColumn('photo_path');
            $table->dropColumn('balance');
            $table->dropForeign(['creator_id']);
            $table->dropColumn('creator_id');
        });
    }
};

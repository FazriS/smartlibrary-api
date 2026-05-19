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
        Schema::create('reading_lists', function (Blueprint $table) {
            $table->id(); // Primary Key [cite: 206]

            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade'); // Foreign Key ke users [cite: 206]

            $table->foreignId('book_id')
                  ->constrained()
                  ->onDelete('cascade'); // Foreign Key ke books [cite: 206]

            $table->enum('status', [
                'want_to_read',
                'reading',
                'finished'
            ])->default('want_to_read'); // Status bacaan [cite: 206]

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_lists');
    }
};
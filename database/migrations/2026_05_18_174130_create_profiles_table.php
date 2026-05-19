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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id(); // Primary Key [cite: 182]
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade'); // Foreign Key ke users [cite: 183]

            $table->text('bio')->nullable(); // Deskripsi profil [cite: 183]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
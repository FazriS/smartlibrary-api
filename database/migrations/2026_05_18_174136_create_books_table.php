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
        Schema::create('books', function (Blueprint $table) {
            $table->id(); // Primary Key [cite: 188]
            $table->string('title'); // Judul buku [cite: 188]
            $table->string('author'); // Nama penulis [cite: 188]
            $table->text('description')->nullable(); // Deskripsi buku [cite: 188]
            $table->year('publish_year'); // Tahun terbit [cite: 188]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
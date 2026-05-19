<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Admin
        $admin = User::create([
            'name' => 'Admin SmartLibrary',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
        Profile::create([
            'user_id' => $admin->id,
            'bio' => 'Akun Administrator Utama Sistem SmartLibrary.'
        ]);

        // 2. Buat Akun User Umum (Doni)
        $user = User::create([
            'name' => 'Doni',
            'email' => 'doni@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'user',
        ]);
        Profile::create([
            'user_id' => $user->id,
            'bio' => 'Mahasiswa Program Studi Teknologi Informasi di Universitas Brawijaya.'
        ]);

        // 3. Buat Master Kategori
        $kategoriTekno = Category::create(['name' => 'Teknologi']);
        $kategoriSains = Category::create(['name' => 'Sains']);
        $kategoriNovel = Category::create(['name' => 'Novel']);

        // 4. Buat Data Buku Master
        $buku1 = Book::create([
            'title' => 'Simulasi IoT Botnet dengan Mirai Framework',
            'author' => 'Doni',
            'description' => 'Buku analisis infrastruktur virtual terhadap serangan siber botnet.',
            'publish_year' => 2026
        ]);

        $buku2 = Book::create([
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'description' => 'Novel inspiratif tentang perjuangan anak-anak di Belitong.',
            'publish_year' => 2005
        ]);

        // 5. Hubungkan Buku dengan Kategori (Relasi Many-to-Many via Pivot)
        $buku1->categories()->attach([$kategoriTekno->id, $kategoriSains->id]);
        $buku2->categories()->attach([$kategoriNovel->id]);
    }
}
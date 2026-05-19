<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    #[OA\Post(
        path: "/api/profiles",
        summary: "Membuat atau memperbarui profil pengguna aktif",
        tags: ["User & Profile"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["bio"],
            properties: [
                new OA\Property(property: "bio", type: "string", example: "Halo, saya Doni, seorang mahasiswa Teknologi Informasi.")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Profil berhasil disimpan")]
    #[OA\Response(response: 400, description: "Validasi gagal")]
    #[OA\Response(response: 401, description: "Unauthorized")]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bio' => 'required|string|max:1000'
        ]);

        // Menggunakan id user yang sedang login dari token JWT
        $profile = Profile::updateOrCreate(
            ['user_id' => auth()->id()],
            ['bio' => $validated['bio']]
        );

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $profile
        ], 201);
    }

    #[OA\Get(
        path: "/api/profiles/{id}",
        summary: "Melihat detail profil berdasarkan ID",
        tags: ["User & Profile"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID dari profil yang ingin dilihat",
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(response: 200, description: "Profil ditemukan")]
    #[OA\Response(response: 401, description: "Unauthorized")]
    #[OA\Response(response: 404, description: "Profil tidak ditemukan")]
    public function show($id)
    {
        // Memuat profil beserta data user terkait (Eager Loading)
        $profile = Profile::with('user')->find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $profile
        ]);
    }
}
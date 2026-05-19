<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/register",
        summary: "Registrasi Pengguna Baru",
        tags: ["Authentication"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "email", "password"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Doni"),
                new OA\Property(property: "email", type: "string", format: "email", example: "doni@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "secret123")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "User berhasil terdaftar")]
    #[OA\Response(response: 400, description: "Validasi input gagal")]
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = auth()->login($user);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil terdaftar',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ]
        ], 201);
    }

    #[OA\Post(
        path: "/api/login",
        summary: "Login Pengguna",
        tags: ["Authentication"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "password"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "doni@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "secret123")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Login berhasil")]
    #[OA\Response(response: 401, description: "Unauthorized")]
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token
            ]
        ]);
    }

    #[OA\Get(
        path: "/api/me",
        summary: "Get Current User",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]] // Mengaktifkan tombol gembok otorisasi di Swagger
    )]
    #[OA\Response(response: 200, description: "Data user berhasil diambil")]
    #[OA\Response(response: 401, description: "Unauthorized")]
    public function me()
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    #[OA\Get(
        path: "/api/users",
        summary: "Get All Users",
        tags: ["Users"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Daftar user berhasil diambil")]
    #[OA\Response(response: 401, description: "Unauthorized")]
    public function getUsers()
    {
        $users = User::with('profile')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    #[OA\Get(
        path: "/api/users/{id}",
        summary: "Get User By ID",
        tags: ["Users"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID User",
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(response: 200, description: "Data user berhasil diambil")]
    #[OA\Response(response: 401, description: "Unauthorized")]
    #[OA\Response(response: 404, description: "User tidak ditemukan")]
    public function getUserById($id)
    {
        $user = User::with('profile')->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Logout User",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Logout berhasil")]
    #[OA\Response(response: 401, description: "Unauthorized")]
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}
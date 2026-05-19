<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Book;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: "/api/categories",
        summary: "Menampilkan semua daftar kategori buku",
        tags: ["Category"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Sukses mengambil data kategori")]
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    #[OA\Post(
        path: "/api/categories",
        summary: "Menambahkan kategori baru",
        tags: ["Category"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Novel")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Kategori berhasil dibuat")]
    #[OA\Response(response: 400, description: "Validasi gagal")]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dibuat',
            'data' => $category
        ], 201);
    }

    #[OA\Get(
        path: "/api/categories/{id}",
        summary: "Melihat detail satu kategori",
        tags: ["Category"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(response: 200, description: "Kategori ditemukan")]
    #[OA\Response(response: 404, description: "Kategori tidak ditemukan")]
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    #[OA\Put(
        path: "/api/books/{id}/category/{categoryId}",
        summary: "Menghubungkan buku dengan kategori tertentu (Many-to-Many)",
        tags: ["Category"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, description: "ID Buku", schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "categoryId", in: "path", required: true, description: "ID Kategori", schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Kategori berhasil ditambahkan ke buku")]
    #[OA\Response(response: 404, description: "Buku atau Kategori tidak ditemukan")]
    public function attachCategory($bookId, $categoryId)
    {
        $book = Book::find($bookId);
        $category = Category::find($categoryId);

        if (!$book || !$category) {
            return response()->json([
                'success' => false,
                'message' => 'Buku atau Kategori tidak ditemukan'
            ], 404);
        }

        // syncWithoutDetaching mencegah duplikasi data relasi di tabel pivot
        $book->categories()->syncWithoutDetaching([$categoryId]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan ke buku ini'
        ]);
    }

    #[OA\Get(
        path: "/api/books/{id}/categories",
        summary: "Melihat daftar kategori yang dimiliki suatu buku",
        tags: ["Category"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Sukses mengambil kategori dari buku")]
    #[OA\Response(response: 404, description: "Buku tidak ditemukan")]
    public function getBookCategories($id)
    {
        $book = Book::with('categories')->find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $book->categories
        ]);
    }
}
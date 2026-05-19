<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class BookController extends Controller
{
    #[OA\Get(
        path: "/api/books",
        summary: "Menampilkan semua daftar buku",
        tags: ["Book"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Sukses mengambil data buku")]
    #[OA\Response(response: 401, description: "Unauthorized")]
    public function index()
    {
        $books = Book::all();
        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }

    #[OA\Post(
        path: "/api/books",
        summary: "Menambahkan data buku baru",
        tags: ["Book"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["title", "author", "publish_year"],
            properties: [
                new OA\Property(property: "title", type: "string", example: "Laskar Pelangi"),
                new OA\Property(property: "author", type: "string", example: "Andrea Hirata"),
                new OA\Property(property: "description", type: "string", example: "Novel tentang perjuangan anak-anak Belitong."),
                new OA\Property(property: "publish_year", type: "integer", example: 2005)
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Buku berhasil ditambahkan")]
    #[OA\Response(response: 400, description: "Validasi gagal")]
    #[OA\Response(response: 401, description: "Unauthorized")]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'publish_year' => 'required|integer|digits:4',
        ]);

        $book = Book::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil ditambahkan',
            'data' => $book
        ], 201);
    }

    #[OA\Get(
        path: "/api/books/{id}",
        summary: "Melihat detail satu buku berdasarkan ID",
        tags: ["Book"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID dari buku yang dicari",
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(response: 200, description: "Data buku ditemukan")]
    #[OA\Response(response: 404, description: "Buku tidak ditemukan")]
    public function show($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $book
        ]);
    }

    #[OA\Put(
        path: "/api/books/{id}",
        summary: "Memperbarui data buku berdasarkan ID",
        tags: ["Book"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID dari buku yang akan diubah",
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "title", type: "string", example: "Laskar Pelangi Edisi Revisi"),
                new OA\Property(property: "author", type: "string", example: "Andrea Hirata"),
                new OA\Property(property: "description", type: "string", example: "Deskripsi diperbarui."),
                new OA\Property(property: "publish_year", type: "integer", example: 2005)
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Buku berhasil diperbarui")]
    #[OA\Response(response: 404, description: "Buku tidak ditemukan")]
    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'publish_year' => 'sometimes|required|integer|digits:4',
        ]);

        $book->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil diperbarui',
            'data' => $book
        ]);
    }

    #[OA\Delete(
        path: "/api/books/{id}",
        summary: "Menghapus data buku berdasarkan ID",
        tags: ["Book"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID dari buku yang akan dihapus",
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(response: 200, description: "Buku berhasil dihapus")]
    #[OA\Response(response: 404, description: "Buku tidak ditemukan")]
    public function destroy($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus'
        ]);
    }
}
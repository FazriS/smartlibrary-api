<?php

namespace App\Http\Controllers;

use App\Models\ReadingList;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReadingListController extends Controller
{
    #[OA\Get(
        path: "/api/reading-lists",
        summary: "Menampilkan semua daftar bacaan milik pengguna aktif",
        tags: ["Reading List"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(response: 200, description: "Sukses mengambil data daftar bacaan")]
    public function index()
    {
        $list = ReadingList::with('book')->where('user_id', auth()->id())->get();
        return response()->json([
            'success' => true,
            'data' => $list
        ]);
    }

    #[OA\Post(
        path: "/api/reading-lists",
        summary: "Menambahkan buku ke dalam daftar bacaan",
        tags: ["Reading List"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["book_id"],
            properties: [
                new OA\Property(property: "book_id", type: "integer", example: 1),
                new OA\Property(property: "status", type: "string", enum: ["want_to_read", "reading", "finished"], example: "want_to_read")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Buku berhasil dimasukkan ke daftar bacaan")]
    #[OA\Response(response: 400, description: "Validasi gagal")]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'status' => 'nullable|in:want_to_read,reading,finished'
        ]);

        $readingList = ReadingList::create([
            'user_id' => auth()->id(),
            'book_id' => $validated['book_id'],
            'status' => $validated['status'] ?? 'want_to_read'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil ditambahkan ke daftar bacaan',
            'data' => $readingList
        ], 201);
    }

    #[OA\Get(
        path: "/api/reading-lists/{id}",
        summary: "Melihat detail satu item daftar bacaan",
        tags: ["Reading List"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Data item ditemukan")]
    #[OA\Response(response: 404, description: "Item tidak ditemukan")]
    public function show($id)
    {
        $list = ReadingList::with('book')->where('user_id', auth()->id())->find($id);

        if (!$list) {
            return response()->json([
                'success' => false,
                'message' => 'Data daftar bacaan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $list
        ]);
    }

    #[OA\Put(
        path: "/api/reading-lists/{id}",
        summary: "Mengubah status progres bacaan buku",
        tags: ["Reading List"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["status"],
            properties: [
                new OA\Property(property: "status", type: "string", enum: ["want_to_read", "reading", "finished"], example: "reading")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Status bacaan berhasil diperbarui")]
    #[OA\Response(response: 404, description: "Item tidak ditemukan")]
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:want_to_read,reading,finished'
        ]);

        $list = ReadingList::where('user_id', auth()->id())->find($id);

        if (!$list) {
            return response()->json([
                'success' => false,
                'message' => 'Data daftar bacaan tidak ditemukan'
            ], 404);
        }

        $list->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status membaca berhasil diperbarui',
            'data' => $list
        ]);
    }

    #[OA\Delete(
        path: "/api/reading-lists/{id}",
        summary: "Menghapus buku dari daftar bacaan",
        tags: ["Reading List"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Item berhasil dihapus")]
    #[OA\Response(response: 404, description: "Item tidak ditemukan")]
    public function destroy($id)
    {
        $list = ReadingList::where('user_id', auth()->id())->find($id);

        if (!$list) {
            return response()->json([
                'success' => false,
                'message' => 'Data daftar bacaan tidak ditemukan'
            ], 404);
        }

        $list->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus dari daftar bacaan'
        ]);
    }
}
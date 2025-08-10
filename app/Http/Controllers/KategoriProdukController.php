<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class KategoriProdukController extends Controller
{
    
    public function index()
    {
        return response()->json(KategoriProduk::paginate(10)); // 10 item per halaman
    }

    public function all()
    {
        return response()->json([
            'data' => KategoriProduk::all()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|unique:kategori_produk'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $kategori = KategoriProduk::create([
            'nama_kategori' => $request->input('nama_kategori')
        ]);

        return response()->json($kategori, 201);
    }

    public function show($id)
    {
        return response()->json(KategoriProduk::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriProduk::findOrFail($id);
        $kategori->update($request->only('nama_kategori'));
        return response()->json($kategori);
    }

    public function destroy($id)
    {
        // Cek apakah kategori yang akan dihapus adalah 'Uncategories'
        $kategori = KategoriProduk::find($id);
        if (!$kategori) {
            return response()->json(['message' => 'Kategori tidak ditemukan.'], 404);
        }

        if (strtolower($kategori->nama_kategori) === 'uncategories') {
            return response()->json(['message' => 'Kategori "Uncategories" tidak dapat dihapus.'], 403);
        }

        // Pastikan 'Uncategories' ada (id bisa fleksibel)
        $uncat = KategoriProduk::firstOrCreate(
            ['nama_kategori' => 'Uncategories'],
            ['nama_kategori' => 'Uncategories']
        );

        // Pindahkan semua produk yang memiliki kategori ini ke kategori 'Uncategories'
        \App\Models\Produk::where('kategori_id', $id)
            ->update(['kategori_id' => $uncat->id]);

        // Hapus kategori
        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus. Produk dipindahkan ke Uncategories.']);
    }
    
}

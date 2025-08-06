<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with(['kategori', 'satuan']);

        if ($request->has('search') && $request->search != '') {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->paginate($request->per_page ?? 10));
    }

    public function all()
    {
        return response()->json([
            'data' => Produk::all()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_produk'   => 'required|string',
            'deskripsi'     => 'nullable|string',
            'harga'         => 'required|numeric',
            'stok'          => 'required|integer',
            'kategori_id'   => 'required|exists:kategori_produk,id',
            'satuan_id'     => 'required|exists:satuan_produk,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $produk = Produk::create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil ditambahkan',
            'data' => $produk
        ], 201);
    }

    public function show($id)
    {
        return response()->json(
            Produk::with(['kategori', 'satuan'])->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);
        $produk->update($request->all());
        return response()->json($produk);
    }

    public function destroy($id)
    {
        Produk::destroy($id);
        return response()->json(['message' => 'Dihapus']);
    }

    public function bulkDelete(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:produk,id'
        ]);

        $deleted = \App\Models\Produk::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus.',
            'deleted_count' => $deleted
        ]);
    }

    
}

<?php

namespace App\Http\Controllers;

use App\Models\SatuanProduk;
use Illuminate\Http\Request;

class SatuanProdukController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => SatuanProduk::all()
        ]);
    }

    public function show($id)
    {
        $satuan = SatuanProduk::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $satuan
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:50',
        ]);

        $satuan = SatuanProduk::create([
            'nama_satuan' => $request->nama_satuan,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Satuan berhasil ditambahkan',
            'data' => $satuan
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $satuan = SatuanProduk::findOrFail($id);
        $satuan->update($request->only('nama_satuan'));

        return response()->json([
            'status' => 'success',
            'message' => 'Satuan berhasil diperbarui',
            'data' => $satuan
        ]);
    }

    public function destroy($id)
    {
        SatuanProduk::destroy($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Satuan berhasil dihapus'
        ]);
    }
}

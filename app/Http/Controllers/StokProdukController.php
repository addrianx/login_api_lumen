<?php

namespace App\Http\Controllers;

use App\Models\StokProduk;
use App\Models\Produk;
use Illuminate\Http\Request;

class StokProdukController extends Controller
{
    public function index()
    {
        $histori = StokProduk::with('produk')->latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $histori
        ]);
    }

    public function show($id)
    {
        $stok = StokProduk::with('produk')->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $stok
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'tipe' => 'required|in:masuk,keluar',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $stok = StokProduk::create($request->only('produk_id', 'tipe', 'jumlah', 'keterangan'));

        // Update stok di tabel produk
        $produk = Produk::find($request->produk_id);
        if ($request->tipe == 'masuk') {
            $produk->stok += $request->jumlah;
        } else {
            $produk->stok = max(0, $produk->stok - $request->jumlah); // jaga jangan minus
        }
        $produk->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Stok berhasil dicatat',
            'data' => $stok
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tipe' => 'in:masuk,keluar',
            'jumlah' => 'integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $stok = StokProduk::findOrFail($id);
        $stok->update($request->only('tipe', 'jumlah', 'keterangan'));

        return response()->json([
            'status' => 'success',
            'message' => 'Histori stok berhasil diperbarui',
            'data' => $stok
        ]);
    }

    public function destroy($id)
    {
        $stok = StokProduk::findOrFail($id);
        $stok->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Histori stok berhasil dihapus'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokProduk;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

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


    // untuk halaman input stok
    public function all()
    {
        $user = auth()->user();

        $produk = Produk::with([
            'kategori',
            'satuan',
            'stok_produk' => function ($query) use ($user) {
                $query->where('store_id', $user->store_id);
            }
        ])
        ->withSum(['stok_produk as stok_masuk' => function ($query) use ($user) {
            $query->where('store_id', $user->store_id)
                ->where('tipe', 'masuk');
        }], 'jumlah')
        ->withSum(['stok_produk as stok_keluar' => function ($query) use ($user) {
            $query->where('store_id', $user->store_id)
                ->where('tipe', 'keluar');
        }], 'jumlah')
        ->get()
        ->map(function ($item) {
            // Hitung stok akhir tanpa raw SQL
            $item->stok_akhir = ($item->stok_masuk ?? 0) - ($item->stok_keluar ?? 0);
            unset($item->stok_masuk, $item->stok_keluar); // optional, kalau nggak mau dikirim
            return $item;
        });

        return response()->json([
            'data' => $produk
        ]);
    }



    public function store(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'nama_produk'   => 'required|string',
            'deskripsi'     => 'nullable|string',
            'harga'         => 'required|numeric',
            'harga_modal'   => 'required|numeric',
            'stok'          => 'nullable|integer|min:0', // stok opsional
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

        DB::beginTransaction();

        try {
            // Simpan produk
            $produk = Produk::create([
                'nama_produk' => $request->nama_produk,
                'deskripsi'   => $request->deskripsi,
                'harga'       => $request->harga,
                'harga_modal' => $request->harga_modal,
                'kategori_id' => $request->kategori_id,
                'satuan_id'   => $request->satuan_id,
            ]);

            // Jika stok awal diinput
            if ($request->filled('stok') && $request->stok > 0) {
                StokProduk::create([
                    'produk_id'  => $produk->id,
                    'tipe'       => 'masuk',
                    'jumlah'     => $request->stok,
                    'keterangan' => 'Stok awal',
                     'store_id'   => $user->store_id,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Produk berhasil ditambahkan',
                'data' => $produk
            ], 201);

            // Simpan log
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'add_new_product',
                'description' => 'Menambahkan Produk: ' .$produk->nama_produk ,
                'ip_address' => $request->ip()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        return response()->json(
            Produk::with(['kategori', 'satuan'])->findOrFail($id)
        );
    }


    public function update(Request $request, $id)
    {
        $user = $request->user();
        $produk = Produk::findOrFail($id);
        $produk->update($request->all());

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'update_product',
            'description' => 'Memperbarui Produk pada ID: ' .$request->ids ,
            'ip_address' => $request->ip()
        ]);

        return response()->json($produk);
    }


    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        Produk::destroy($id);

        // Simpan log (pastikan pakai $id, bukan $request->ids kalau hapus satuan)
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'deleted_products',
            'description' => 'Menghapus Produk dengan ID: ' . $id,
            'ip_address' => $request->ip()
        ]);

        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }


    public function bulkDelete(Request $request)
    {
        $user = $request->user();
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:produk,id'
        ]);

        $deleted = \App\Models\Produk::whereIn('id', $request->ids)->delete();

        // Simpan log
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'bulk_delete_products',
            'description' => 'Menghapus Produk Bulk dengan ID: ' . implode(',', $request->ids),
            'ip_address' => $request->ip()
        ]);
            
        return response()->json([
            'message' => 'Produk berhasil dihapus.',
            'deleted_count' => $deleted
        ]);
    }

    
}

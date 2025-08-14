<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\StokProduk;

class StokProdukController extends Controller
{


    public function index()
    {
        $user = auth()->user();
        // Ambil semua histori stok dengan relasi produk, kategori, dan satuan
        $histori = StokProduk::with(['produk.kategori', 'produk.satuan', 'store'])
            ->where('store_id', $user->store_id) // aman, filter dari login
            ->latest()
            ->get();

        $produkStok = [];

        foreach ($histori as $item) {
            $produk = $item->produk;
            $produkId = $produk->id;

            if (!isset($produkStok[$produkId])) {
                $produkStok[$produkId] = [
                    'produk_id' => $produkId,
                    'nama_produk' => $produk->nama_produk,
                    'harga' => $produk->harga,
                    'kategori' => $produk->kategori->nama_kategori ?? null,
                    'satuan' => $produk->satuan->nama_satuan ?? null,
                    'store' => $item->store ? [
                        'id' => $item->store->id,
                        'nama' => $item->store->nama,
                        'alamat' => $item->store->alamat,
                        'no_kontak' => $item->store->no_kontak,
                    ] : null,
                    'total_masuk' => 0,
                    'total_keluar' => 0,
                    'stok_akhir' => 0, // nanti dihitung manual
                    'terakhir_update' => $item->created_at?->format('Y-m-d H:i:s') ?? null
                ];
            }


            if ($item->tipe === 'masuk') {
                $produkStok[$produkId]['total_masuk'] += $item->jumlah;
            } elseif ($item->tipe === 'keluar') {
                $produkStok[$produkId]['total_keluar'] += $item->jumlah;
            }

            $existingDate = $produkStok[$produkId]['terakhir_update'];
            $currentDate = $item->created_at?->format('Y-m-d H:i:s');
            if ($currentDate > $existingDate) {
                $produkStok[$produkId]['terakhir_update'] = $currentDate;
            }
        }


        // Hitung stok akhir & stok awal
        foreach ($produkStok as &$data) {
            $data['stok_akhir'] = $data['total_masuk'] - $data['total_keluar'];
            $data['stok_awal'] = $data['stok_akhir'] - $data['total_masuk'] + $data['total_keluar'];
        }

        return response()->json([
            'status' => 'success',
            'data' => array_values($produkStok)
        ]);
    }


    public function paginate(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->input('per_page', 10); // default 10 per halaman

        // Ambil histori stok terurut dari yang terbaru, termasuk store
        $historiQuery = StokProduk::with([
            'produk.kategori', 
            'produk.satuan',
            'store'
        ])
        ->where('store_id', $user->store_id) // hanya stok dari toko user
        ->latest();

        $historiPaginated = $historiQuery->paginate($perPage);

        // Kelompokkan histori berdasarkan produk_id
        $grouped = collect($historiPaginated->items())->groupBy('produk_id');

        $data = [];

        foreach ($grouped as $produkId => $items) {
            $produk = $items->first()->produk;
            $store  = $items->first()->store;

            $produkStok = [
                'produk_id' => $produkId,
                'nama_produk' => $produk->nama_produk,
                'kategori' => $produk->kategori->nama_kategori ?? null,
                'satuan' => $produk->satuan->nama_satuan ?? null,
                'store' => [
                    'id' => $store->id ?? null,
                    'nama' => $store->name ?? null,
                    'alamat' => $store->address ?? null,
                    'no_kontak' => $store->phone ?? null,
                ],
                'total_masuk' => 0,
                'total_keluar' => 0,
                'stok_akhir' => $produk->stok,
                'stok_awal' => null,
                'terakhir_update' => null,
                'riwayat' => [],
            ];

            foreach ($items as $item) {
                if ($item->tipe === 'masuk') {
                    $produkStok['total_masuk'] += $item->jumlah;
                } elseif ($item->tipe === 'keluar') {
                    $produkStok['total_keluar'] += $item->jumlah;
                }

                $currentDate = $item->created_at?->format('Y-m-d H:i:s');
                if (!$produkStok['terakhir_update'] || $currentDate > $produkStok['terakhir_update']) {
                    $produkStok['terakhir_update'] = $currentDate;
                }

                $produkStok['riwayat'][] = [
                    'tipe' => $item->tipe,
                    'jumlah' => $item->jumlah,
                    'keterangan' => $item->keterangan,
                    'waktu' => $currentDate
                ];
            }

            // Hitung stok awal
            $produkStok['stok_awal'] = $produkStok['stok_akhir'] - $produkStok['total_masuk'] + $produkStok['total_keluar'];

            $data[] = $produkStok;
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'pagination' => [
                'current_page' => $historiPaginated->currentPage(),
                'last_page' => $historiPaginated->lastPage(),
                'per_page' => $historiPaginated->perPage(),
                'total' => $historiPaginated->total()
            ]
        ]);
    }


    public function show($id)
    {
        $user = auth()->user();

        $stok = StokProduk::with(['produk', 'store'])
            ->where('store_id', $user->store_id) // Hanya stok dari toko user
            ->find($id);

        if (!$stok) {
            return response()->json([
                'status' => 'error',
                'message' => "Stok dengan ID $id tidak ditemukan atau tidak punya akses"
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $stok
        ]);
    }


    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'produk_id' => 'required|exists:produk,id',
            'tipe' => 'required|in:masuk,keluar',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
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
            // Perhitungan stok real-time dari tabel stok_produk
            if ($request->tipe === 'keluar') {
                $totalMasuk = StokProduk::where('produk_id', $request->produk_id)
                    ->where('tipe', 'masuk')
                    ->sum('jumlah');

                $totalKeluar = StokProduk::where('produk_id', $request->produk_id)
                    ->where('tipe', 'keluar')
                    ->sum('jumlah');

                $stokTersedia = $totalMasuk - $totalKeluar;

                if ($stokTersedia < $request->jumlah) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Jumlah keluar melebihi stok yang tersedia'
                    ], 400);
                }
            }

            // Simpan histori stok
            $stok = StokProduk::create([
                'produk_id' => $request->produk_id,
                'store_id' => $user->store_id,
                'tipe' => $request->tipe,
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Histori stok berhasil disimpan',
                'data' => $stok
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
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

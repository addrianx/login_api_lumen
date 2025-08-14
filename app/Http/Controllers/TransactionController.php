<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StokProduk;
use App\Models\Customer;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        // Validasi input transaksi
        $validator = Validator::make($request->all(), [
            'customer_id'       => 'required|exists:customers,id',
            'items'             => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.quantity'  => 'required|integer|min:1',
            'subtotal'          => 'required|numeric|min:0',
            'diskon'            => 'nullable|numeric|min:0',
            'total'             => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|in:tunai,qris,transfer',
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
            // Simpan transaksi utama
            $transaction = Transaction::create([
                'customer_id'       => $request->customer_id,
                'subtotal'          => $request->subtotal,
                'store_id' => $user->store_id,
                'diskon'            => $request->diskon ?? 0,
                'total'             => $request->total,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status'            => 'completed', // bisa disesuaikan
                'user_id'           => auth()->id()
            ]);

            // Simpan tiap item transaksi & update stok
            foreach ($request->items as $item) {
                // Ambil data produk untuk validasi stok dan harga (opsional)
                $produk = Produk::find($item['produk_id']);
                if (!$produk) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => "Produk dengan ID {$item['produk_id']} tidak ditemukan"
                    ], 404);
                }

                // Optional: Cek stok cukup atau tidak
                $stokAkhir = $produk->stokAkhir;
                if ($stokAkhir < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stok produk '{$produk->nama_produk}' tidak cukup. Tersisa: {$stokAkhir}"
                    ], 400);
                }

                // Simpan item transaksi
                $subtotalItem = $produk->harga * $item['quantity'];
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'produk_id'      => $produk->id,
                    'quantity'       => $item['quantity'],
                    'harga_satuan'   => $produk->harga,
                    'subtotal'       => $subtotalItem,
                ]);

                // Update stok keluar
                StokProduk::create([
                    'produk_id'  => $produk->id,
                    'tipe'       => 'keluar',
                    'jumlah'     => $item['quantity'],
                    'keterangan' => "Penjualan transaksi ID #{$transaction->id}"
                ]);


            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil disimpan',
                'data' => $transaction->load('items', 'customer')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan transaksi',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = Transaction::with(['customer', 'items.produk', 'user', 'store'])
            ->where('store_id', $user->store_id) // Filter hanya toko user login
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('nama_customer', 'like', '%' . $search . '%');
            });
        }

        $transactions = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();

        $transaction = Transaction::with(['customer', 'items.produk', 'store'])
            ->where('store_id', $user->store_id) // Pastikan hanya bisa akses toko sendiri
            ->find($id);

        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => "Transaksi dengan ID $id tidak ditemukan"
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $transaction
        ]);
    }

}

<?php
// app/Http/Controllers/CustomerController.php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PurchaseHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    // List pelanggan
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = Customer::with('transactions')
            ->where('store_id', $user->store_id);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        $customers = $query->orderBy('name', 'asc')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $customers
        ]);
    }


    // Tambah pelanggan
    public function store(Request $request)
    {
        $user = auth()->user();

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20|unique:customers',
            'address' => 'nullable|string',
        ]);

        // Kalau validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Ambil data hasil validasi
        $data = $validator->validated();

        // Tambahkan store_id
        $data['store_id'] = $user->store_id;

        // Simpan data customer
        $customer = Customer::create($data);

        return response()->json($customer, 201);
    }

    // Detail pelanggan
    public function show($id)
    {
        $customer = Customer::with('purchaseHistories')->findOrFail($id);
        return response()->json($customer);
    }

    // Update pelanggan
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name'    => 'sometimes|required|string|max:255',
            'phone'   => 'sometimes|required|string|max:20|unique:customers,phone,' . $id,
            'address' => 'nullable|string',
            'loyalty_points' => 'integer|min:0'
        ]);

        $customer->update($validated);
        return response()->json($customer);
    }

    // Hapus pelanggan
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['message' => 'Customer deleted']);
    }

    // Tambah riwayat pembelian
    // public function addPurchase(Request $request, $customerId)
    // {
    //     $validated = $request->validate([
    //         'product_name' => 'required|string',
    //         'quantity'     => 'required|integer|min:1',
    //         'price'        => 'required|numeric|min:0'
    //     ]);

    //     $purchase = PurchaseHistory::create([
    //         'customer_id' => $customerId,
    //         'product_name' => $validated['product_name'],
    //         'quantity'     => $validated['quantity'],
    //         'price'        => $validated['price'],
    //     ]);

    //     // Tambah poin loyalitas (misal 1 poin per Rp 10.000)
    //     $customer = Customer::findOrFail($customerId);
    //     $customer->loyalty_points += floor($validated['price'] * $validated['quantity'] / 10000);
    //     $customer->save();

    //     return response()->json($purchase, 201);
    // }

    public function bulkDelete(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:customers,id',
            'password' => 'required|string'
        ]);

        $user = auth()->user();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Password tidak sesuai.'
            ], 401);
        }

        $deleted = Customer::whereIn('id', $request->ids)->delete();

        return response()->json([
            'message' => 'Customer berhasil dihapus.',
            'deleted_count' => $deleted
        ]);
    }
    
}

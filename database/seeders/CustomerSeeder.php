<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $customers = [
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'address' => 'Jl. Melati No. 10, Jakarta',
                'loyalty_points' => 120
            ],
            [
                'name' => 'Siti Aminah',
                'phone' => '081987654321',
                'address' => 'Jl. Kenanga No. 5, Bandung',
                'loyalty_points' => 250
            ],
            [
                'name' => 'Andi Wijaya',
                'phone' => '085612345678',
                'address' => 'Jl. Mawar No. 7, Surabaya',
                'loyalty_points' => 90
            ],
            [
                'name' => 'Rina Kartika',
                'phone' => '081322334455',
                'address' => 'Jl. Anggrek No. 2, Yogyakarta',
                'loyalty_points' => 300
            ]
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}

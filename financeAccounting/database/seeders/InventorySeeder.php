<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        Inventory::insert([
            ['item_name' => 'Office Chair', 'quantity' => 10, 'price' => 3500, 'created_at' => now(), 'updated_at' => now()],
            ['item_name' => 'Laptop Desk', 'quantity' => 5, 'price' => 8500, 'created_at' => now(), 'updated_at' => now()],
            ['item_name' => 'Monitor 24"', 'quantity' => 8, 'price' => 7200, 'created_at' => now(), 'updated_at' => now()],
            ['item_name' => 'Keyboard Wireless', 'quantity' => 15, 'price' => 1200, 'created_at' => now(), 'updated_at' => now()],
            ['item_name' => 'Mouse Optical', 'quantity' => 20, 'price' => 450, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

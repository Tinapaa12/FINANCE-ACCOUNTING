<?php
namespace Database\Seeders;

use App\Models\Inventory\Inventory;
use App\Models\Inventory\InventoryTransaction;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['item_name' => '[SR-2026-001] Office Chair', 'sku' => 'FURN-001', 'quantity' => 10, 'unit_cost' => 2500, 'selling_price' => 3500, 'category' => 'Furniture'],
            ['item_name' => '[SR-2026-002] Laptop Desk', 'sku' => 'FURN-002', 'quantity' => 5, 'unit_cost' => 6500, 'selling_price' => 8500, 'category' => 'Furniture'],
            ['item_name' => '[SR-2026-003] Monitor 24"', 'sku' => 'ELEC-001', 'quantity' => 8, 'unit_cost' => 5500, 'selling_price' => 7200, 'category' => 'Electronics'],
            ['item_name' => '[SR-2026-004] Keyboard Wireless', 'sku' => 'ELEC-002', 'quantity' => 15, 'unit_cost' => 800, 'selling_price' => 1200, 'category' => 'Electronics'],
            ['item_name' => '[SR-2026-005] Mouse Optical', 'sku' => 'ELEC-003', 'quantity' => 20, 'unit_cost' => 300, 'selling_price' => 450, 'category' => 'Electronics'],
            ['item_name' => '[SR-2026-006] Printer Paper (ream)', 'sku' => 'SUPP-001', 'quantity' => 50, 'unit_cost' => 180, 'selling_price' => 250, 'category' => 'Supplies'],
        ];

        foreach ($items as $data) {
            $item = Inventory::create($data);
            InventoryTransaction::create([
                'inventory_id' => $item->id,
                'type' => 'in',
                'quantity' => $data['quantity'],
                'unit_cost' => $data['unit_cost'],
                'total' => $data['quantity'] * $data['unit_cost'],
                'notes' => 'Initial stock',
            ]);
        }
    }
}

<?php
namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Sales\SalesTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    public function run(): void
    {
        // === CUSTOMERS ===
        $customers = [
            ['name' => 'Dela Cruz Hardware', 'email' => 'dcruz@example.com', 'phone' => '09171234567', 'address' => '123 Rizal St, Manila'],
            ['name' => 'Mendoza Appliances', 'email' => 'mendoza@example.com', 'phone' => '09182345678', 'address' => '456 Mabini Ave, Quezon City'],
            ['name' => 'Fernandez Trading', 'email' => 'fernandez@example.com', 'phone' => '09193456789', 'address' => '789 Del Pilar St, Makati'],
        ];

        $createdCustomers = [];
        foreach ($customers as $data) {
            $createdCustomers[] = Customer::firstOrCreate(['name' => $data['name']], $data);
        }

        // === INVOICES ===
        $invoices = [
            ['customer' => 0, 'status' => 'sent',   'days_offset' => -10, 'total' => 95000],
            ['customer' => 1, 'status' => 'cleared', 'days_offset' => -45, 'total' => 210000],
            ['customer' => 2, 'status' => 'overdue', 'days_offset' => -20, 'total' => 78000],
        ];

        foreach ($invoices as $i => $inv) {
            $customer = $createdCustomers[$inv['customer']];
            $invDate = now()->addDays($inv['days_offset']);
            $dueDate = $invDate->copy()->addDays(30);
            $vatAmount = round($inv['total'] * 0.12 / 1.12, 2);
            $subtotal = round($inv['total'] - $vatAmount, 2);
            $num = 'INV-2026-' . str_pad(16 + $i, 3, '0', STR_PAD_LEFT);

            Invoice::firstOrCreate(
                ['invoice_number' => $num],
                [
                    'customer_id'  => $customer->id,
                    'type'         => 'invoice',
                    'invoice_date' => $invDate->format('Y-m-d'),
                    'due_date'     => $dueDate->format('Y-m-d'),
                    'currency'     => 'PHP',
                    'subtotal'     => $subtotal,
                    'vat_amount'   => $vatAmount,
                    'total'        => $inv['total'],
                    'status'       => $inv['status'],
                    'notes'        => '[SalesSeeder]',
                ]
            );
        }

        // === SALES TRANSACTIONS ===
        $sales = [
            ['order_no' => 'SO-2026-004', 'customer' => 'Dela Cruz Hardware', 'amount' => 45000, 'method' => 'Bank Transfer', 'status' => 'Paid'],
            ['order_no' => 'SO-2026-005', 'customer' => 'Mendoza Appliances', 'amount' => 120000, 'method' => 'Cash', 'status' => 'Paid'],
            ['order_no' => 'SO-2026-006', 'customer' => 'Fernandez Trading', 'amount' => 67000, 'method' => 'Credit Card', 'status' => 'Pending'],
            ['order_no' => 'SO-2026-007', 'customer' => 'Walk-in Customer', 'amount' => 8500, 'method' => 'Cash', 'status' => 'Paid'],
        ];

        foreach ($sales as $s) {
            SalesTransaction::create([
                'order_no' => $s['order_no'],
                'customer_name' => '[SalesSeeder] ' . $s['customer'],
                'total_amount' => $s['amount'],
                'payment_method' => $s['method'],
                'status' => $s['status'],
                'is_posted_to_finance' => $s['status'] === 'Paid',
            ]);
        }
    }
}

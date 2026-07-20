<?php
namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class AgingReportSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Santos Enterprises', 'email' => 'santos@example.com'],
            ['name' => 'ABC Trading Co.', 'email' => 'abc@example.com'],
            ['name' => 'Cruz & Sons', 'email' => 'cruz@example.com'],
            ['name' => 'Reyes Corp', 'email' => 'reyes@example.com'],
            ['name' => 'Lim Trading', 'email' => 'lim@example.com'],
            ['name' => 'Garcia Merchandising', 'email' => 'garcia@example.com'],
        ];

        $createdCustomers = [];
        foreach ($customers as $data) {
            $createdCustomers[] = Customer::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        $invoices = [
            ['customer' => 0, 'status' => 'sent',   'due' => +30, 'total' => 150000],
            ['customer' => 1, 'status' => 'sent',   'due' => +15, 'total' => 85000],
            ['customer' => 2, 'status' => 'overdue', 'due' => -5,  'total' => 220000],
            ['customer' => 3, 'status' => 'overdue', 'due' => -20, 'total' => 95000],
            ['customer' => 4, 'status' => 'overdue', 'due' => -45, 'total' => 180000],
            ['customer' => 5, 'status' => 'overdue', 'due' => -30, 'total' => 120000],
            ['customer' => 0, 'status' => 'overdue', 'due' => -75, 'total' => 300000],
            ['customer' => 1, 'status' => 'overdue', 'due' => -95, 'total' => 65000],
            ['customer' => 2, 'status' => 'cleared', 'due' => -60, 'total' => 175000],
            ['customer' => 3, 'status' => 'cleared', 'due' => -40, 'total' => 140000],
            ['customer' => 4, 'status' => 'sent',   'due' => +60, 'total' => 50000],
            ['customer' => 5, 'status' => 'overdue', 'due' => -10, 'total' => 250000],
            ['customer' => 0, 'status' => 'sent',   'due' => +5,  'total' => 88000],
            ['customer' => 1, 'status' => 'overdue', 'due' => -60, 'total' => 195000],
            ['customer' => 2, 'status' => 'sent',   'due' => +45, 'total' => 72000],
        ];

        foreach ($invoices as $i => $inv) {
            $customer = $createdCustomers[$inv['customer']];
            $dueDate = now()->addDays($inv['due']);
            $vatAmount = round($inv['total'] * 0.12 / 1.12, 2);
            $subtotal = round($inv['total'] - $vatAmount, 2);

            $num = 'INV-2026-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
            Invoice::firstOrCreate(
                ['invoice_number' => $num],
                [
                    'customer_id'  => $customer->id,
                    'type'         => 'invoice',
                    'invoice_date' => $dueDate->copy()->subDays(30),
                    'due_date'     => $dueDate,
                    'currency'     => 'PHP',
                    'subtotal'     => $subtotal,
                    'vat_amount'   => $vatAmount,
                    'total'        => $inv['total'],
                    'status'       => $inv['status'],
                ]
            );
        }
    }
}

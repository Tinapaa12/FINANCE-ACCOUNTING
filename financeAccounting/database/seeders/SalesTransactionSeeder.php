<?php
namespace Database\Seeders;

use App\Models\Sales\SalesTransaction;
use Illuminate\Database\Seeder;

class SalesTransactionSeeder extends Seeder
{
    public function run()
    {
        $invoices = [
            ['order_no' => 'INV-0001', 'customer_name' => 'Santos Enterprises',    'total_amount' => 45000,  'invoice_date' => '2026-05-01', 'due_date' => '2026-05-31', 'invoice_type' => 'Invoice', 'currency' => 'PHP', 'subtotal' => 40178.57, 'vat_amount' => 4821.43, 'status' => 'Overdue', 'payment_method' => 'Bank Transfer', 'line_items' => json_encode([['desc' => 'Consulting Services', 'qty' => 1, 'price' => 40178.57, 'vat' => 12]])],
            ['order_no' => 'INV-0002', 'customer_name' => 'ABC Trading',           'total_amount' => 38500,  'invoice_date' => '2026-06-15', 'due_date' => '2026-07-15', 'invoice_type' => 'Invoice', 'currency' => 'PHP', 'subtotal' => 34375.00, 'vat_amount' => 4125.00, 'status' => 'Sent', 'payment_method' => 'Credit Card', 'line_items' => json_encode([['desc' => 'Office Supplies', 'qty' => 50, 'price' => 687.50, 'vat' => 12]])],
            ['order_no' => 'INV-0003', 'customer_name' => 'Cruz & Sons',           'total_amount' => 12000,  'invoice_date' => '2026-06-20', 'due_date' => '2026-07-20', 'invoice_type' => 'Invoice', 'currency' => 'PHP', 'subtotal' => 10714.29, 'vat_amount' => 1285.71, 'status' => 'Cleared', 'payment_method' => 'Cash', 'line_items' => json_encode([['desc' => 'Maintenance', 'qty' => 1, 'price' => 10714.29, 'vat' => 12]])],
            ['order_no' => 'INV-0004', 'customer_name' => 'Reyes Corporation',     'total_amount' => 78000,  'invoice_date' => '2026-06-22', 'due_date' => '2026-07-22', 'invoice_type' => 'Invoice', 'currency' => 'PHP', 'subtotal' => 69642.86, 'vat_amount' => 8357.14, 'status' => 'Draft', 'payment_method' => 'Bank Transfer', 'line_items' => json_encode([['desc' => 'Software License', 'qty' => 1, 'price' => 69642.86, 'vat' => 12]])],
            ['order_no' => 'INV-0005', 'customer_name' => 'Lim Trading',           'total_amount' => 19500,  'invoice_date' => '2026-06-25', 'due_date' => '2026-07-25', 'invoice_type' => 'Invoice', 'currency' => 'PHP', 'subtotal' => 17410.71, 'vat_amount' => 2089.29, 'status' => 'Sent', 'payment_method' => 'Credit Card', 'line_items' => json_encode([['desc' => 'Delivery Service', 'qty' => 1, 'price' => 17410.71, 'vat' => 12]])],
            ['order_no' => 'INV-0006', 'customer_name' => 'Santos Enterprises',    'total_amount' => 28500,  'invoice_date' => '2026-06-28', 'due_date' => '2026-07-28', 'invoice_type' => 'Invoice', 'currency' => 'PHP', 'subtotal' => 25446.43, 'vat_amount' => 3053.57, 'status' => 'Draft', 'payment_method' => 'Installment', 'line_items' => json_encode([['desc' => 'Equipment Rental', 'qty' => 1, 'price' => 25446.43, 'vat' => 12]])],
            ['order_no' => 'INV-0007', 'customer_name' => 'ABC Trading',           'total_amount' => 22000,  'invoice_date' => '2026-06-30', 'due_date' => '2026-07-30', 'invoice_type' => 'Invoice', 'currency' => 'PHP', 'subtotal' => 19642.86, 'vat_amount' => 2357.14, 'status' => 'Draft', 'payment_method' => 'Bank Transfer', 'line_items' => json_encode([['desc' => 'Logistics', 'qty' => 1, 'price' => 19642.86, 'vat' => 12]])],
            ['order_no' => 'INV-0008', 'customer_name' => 'Lim Trading',           'total_amount' => 10000,  'invoice_date' => '2026-06-25', 'due_date' => '2026-07-25', 'invoice_type' => 'Payment', 'currency' => 'PHP', 'subtotal' => 10000.00, 'vat_amount' => 0.00, 'status' => 'Cleared', 'payment_method' => 'Cash', 'line_items' => json_encode([['desc' => 'Partial Payment', 'qty' => 1, 'price' => 10000.00, 'vat' => 0]])],
            ['order_no' => 'INV-0009', 'customer_name' => 'Cruz & Sons',           'total_amount' => 56000,  'invoice_date' => '2026-05-15', 'due_date' => '2026-06-14', 'invoice_type' => 'Invoice', 'currency' => 'PHP', 'subtotal' => 50000.00, 'vat_amount' => 6000.00, 'status' => 'Overdue', 'payment_method' => 'Bank Transfer', 'line_items' => json_encode([['desc' => 'Construction Materials', 'qty' => 1, 'price' => 50000.00, 'vat' => 12]])],
            ['order_no' => 'INV-0010', 'customer_name' => 'Reyes Corporation',     'total_amount' => 16500,  'invoice_date' => '2026-06-05', 'due_date' => '2026-07-05', 'invoice_type' => 'Invoice', 'currency' => 'PHP', 'subtotal' => 14732.14, 'vat_amount' => 1767.86, 'status' => 'Paid', 'payment_method' => 'Credit Card', 'line_items' => json_encode([['desc' => 'IT Support', 'qty' => 1, 'price' => 14732.14, 'vat' => 12]])],
        ];

        foreach ($invoices as $invoice) {
            SalesTransaction::create($invoice);
        }
    }
}

<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class DunningLetterService
{
    public static function send(array $data): array
    {
        $customer = $data['customer_name'] ?? 'Unknown';
        $total = (float)($data['total_amount'] ?? 0);
        $dueDate = $data['due_date'] ?? null;
        $ref = $data['reference'] ?? 'N/A';
        $phone = $data['phone'] ?? null;

        if ($dueDate && !($dueDate instanceof \Carbon\Carbon)) {
            try {
                $dueDate = \Carbon\Carbon::parse($dueDate);
            } catch (\Exception $e) {
                $dueDate = null;
            }
        }

        $daysOverdue = $dueDate ? max(0, now()->startOfDay()->diffInDays($dueDate, false)) : 0;
        $dueDateStr = $dueDate?->format('M d, Y') ?? 'N/A';

        $message = "Dear {$customer}, this is a dunning notice for {$ref}. ";
        $message .= "Pay your remaining balance: ₱" . number_format($total, 2) . ". ";
        $message .= "Total amount: ₱" . number_format($total, 2) . ". ";
        $message .= "Due date: {$dueDateStr} (" . ($daysOverdue > 0 ? "{$daysOverdue} day(s) overdue" : "due soon") . "). ";
        $message .= "Please pay ASAP to avoid penalties. Thank you.";

        Log::info("Dunning letter sent", [
            'customer' => $customer,
            'phone' => $phone,
            'reference' => $ref,
            'total_amount' => $total,
            'amount_due' => $total,
            'due_date' => $dueDate?->toDateString(),
            'days_overdue' => $daysOverdue,
            'message' => $message,
        ]);

        return [
            'customer' => $customer,
            'phone' => $phone,
            'order_no' => $ref,
            'total_amount' => $total,
            'initial_payment' => 0,
            'amount_due' => $total,
            'due_date' => $dueDate?->toDateString(),
            'days_overdue' => $daysOverdue,
            'message' => $message,
        ];
    }
}

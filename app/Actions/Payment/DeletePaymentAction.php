<?php

declare(strict_types=1);

namespace App\Actions\Payment;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

final readonly class DeletePaymentAction
{
    public function execute(Payment $payment): void
    {
        DB::transaction(function () use ($payment): void {
            $invoice = Invoice::query()
                ->where('id', $payment->invoice_id)
                ->lockForUpdate()
                ->firstOrFail();

            $payment->delete();

            if ($invoice) {
                $this->revertInvoiceStatus($invoice);
            }
        });
    }

    private function revertInvoiceStatus(Invoice $invoice): void
    {
        $totalPaid = round((float) $invoice->payments()->sum('amount'), 2);
        $invoiceTotal = (float) $invoice->total;

        if ($totalPaid < $invoiceTotal && $invoice->status === InvoiceStatus::Paid) {
            $invoice->update(['status' => InvoiceStatus::Sent]);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invoice;

use App\Models\Invoice;
use Illuminate\View\View;

final readonly class PrintInvoiceController
{
    public function __invoke(Invoice $invoice): View
    {
        $invoice->load(['customer', 'items']);

        return view('invoices.print', ['invoice' => $invoice]);
    }
}

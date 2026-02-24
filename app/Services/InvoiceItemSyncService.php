<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Invoices\InvoiceItemData;
use App\Models\Invoice;

final readonly class InvoiceItemSyncService
{
    public function __construct(
        private CalculateInvoiceTotalService $calculateTotals,
    ) {}

    public function sync(Invoice $invoice, array $items): void
    {
        $invoice->items()->delete();

        $invoice->items()->createMany(
            array_map(
                static fn (InvoiceItemData $item): array => [
                    'description' => $item->description,
                    'qty' => $item->qty,
                    'unit_price' => $item->unitPrice,
                    'total' => $item->total(),
                ],
                $items,
            ),
        );
    }

    public function applyTotals(Invoice $invoice, array $items, float $taxRate): void
    {
        $totals = $this->calculateTotals->calculate($items, $taxRate);
        $invoice->update([
            'subtotal' => $totals->subtotal,
            'tax_rate' => $taxRate,
            'tax' => $totals->tax,
            'total' => $totals->total,
        ]);
    }

    public function recalculateInvoiceTotals(Invoice $invoice): void
    {
        $items = $invoice->items()->get();
        $taxRate = (float) $invoice->tax_rate;

        $subtotal = $items->sum('total');
        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $tax;

        $invoice->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }
}

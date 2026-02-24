<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SalesReturn;

final readonly class CalculateInvoiceSalesReturnService
{
    public function __construct(
        private InvoiceItemSyncService $invoiceItemSync,
    ) {}

    public function addReturn(Invoice $invoice, SalesReturn $salesReturn): Invoice
    {
        $this->reduceInvoiceItemQuantities($salesReturn);
        $this->invoiceItemSync->recalculateInvoiceTotals($invoice);

        $currentReturnTotal = (float) ($invoice->sales_return_total ?? 0);
        $newReturnTotal = $currentReturnTotal + (float) $salesReturn->total;

        $invoice->update([
            'sales_return_total' => $newReturnTotal,
        ]);

        return $invoice;
    }

    public function removeReturn(Invoice $invoice, SalesReturn $salesReturn): Invoice
    {
        $this->restoreInvoiceItemQuantities($invoice, $salesReturn);
        $this->invoiceItemSync->recalculateInvoiceTotals($invoice);

        $currentReturnTotal = (float) ($invoice->sales_return_total ?? 0);
        $newReturnTotal = max(0, $currentReturnTotal - (float) $salesReturn->total);

        $invoice->update([
            'sales_return_total' => $newReturnTotal,
        ]);

        return $invoice;
    }

    public function getAdjustedTotal(Invoice $invoice): float
    {
        return (float) $invoice->total - (float) ($invoice->sales_return_total ?? 0);
    }

    private function reduceInvoiceItemQuantities(SalesReturn $salesReturn): void
    {
        foreach ($salesReturn->items as $returnItem) {
            if ($returnItem->invoice_item_id) {
                $invoiceItem = InvoiceItem::query()->find($returnItem->invoice_item_id);
                if ($invoiceItem) {
                    $newQty = max(0, (int) $invoiceItem->qty - (int) $returnItem->qty);
                    $newTotal = $newQty * (float) $invoiceItem->unit_price;
                    $invoiceItem->update([
                        'qty' => $newQty,
                        'total' => $newTotal,
                    ]);
                }
            }
        }
    }

    private function restoreInvoiceItemQuantities(Invoice $invoice, SalesReturn $salesReturn): void
    {
        $originalInvoice = Invoice::query()
            ->with('items')
            ->find($invoice->id);

        if (! $originalInvoice) {
            return;
        }

        $originalItemsMap = $originalInvoice->items->keyBy('id');

        foreach ($salesReturn->items as $returnItem) {
            if ($returnItem->invoice_item_id && isset($originalItemsMap[$returnItem->invoice_item_id])) {
                $invoiceItem = InvoiceItem::query()->find($returnItem->invoice_item_id);
                if ($invoiceItem) {
                    $originalItem = $originalItemsMap[$returnItem->invoice_item_id];
                    $newQty = (int) $invoiceItem->qty + (int) $returnItem->qty;
                    $newTotal = $newQty * (float) $invoiceItem->unit_price;
                    $invoiceItem->update([
                        'qty' => $newQty,
                        'total' => $newTotal,
                    ]);
                }
            }
        }
    }
}

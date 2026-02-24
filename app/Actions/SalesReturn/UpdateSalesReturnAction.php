<?php

declare(strict_types=1);

namespace App\Actions\SalesReturn;

use App\Enums\SalesReturnStatus;
use App\Models\Invoice;
use App\Models\SalesReturn;
use App\Services\CalculateInvoiceSalesReturnService;

final readonly class UpdateSalesReturnAction
{
    public function __construct(
        private CalculateInvoiceSalesReturnService $salesReturnService,
    ) {}

    public function execute(SalesReturn $salesReturn, array $data): SalesReturn
    {
        $oldStatus = $salesReturn->status;
        $newStatus = SalesReturnStatus::from($data['status']);

        if (! empty($data['invoice_id'])) {
            $invoice = Invoice::with('items')->find($data['invoice_id']);
            [$itemsToSave, $subtotal] = $this->calculateItemsFromInvoice($data, $invoice);
            $this->updateSalesReturn($salesReturn, $data, $subtotal);
            $this->syncItems($salesReturn, $itemsToSave);

            $salesReturn = $salesReturn->fresh();
            $this->handleStatusChange($salesReturn, $invoice, $oldStatus, $newStatus);
        } else {
            $this->updateSalesReturn($salesReturn, $data, 0);
            $salesReturn->items()->delete();

            if ($salesReturn->invoice) {
                $this->handleStatusChange($salesReturn, $salesReturn->invoice, $oldStatus, $newStatus);
            }
        }

        return $salesReturn->fresh();
    }

    private function calculateItemsFromInvoice(array $data, Invoice $invoice): array
    {
        $itemsToSave = [];
        $subtotal = 0;

        foreach ($invoice->items as $item) {
            $returnQty = (int) ($data['items'][$item->id]['quantity'] ?? 0);
            if ($returnQty > 0) {
                $itemTotal = $returnQty * (float) $item->unit_price;
                $subtotal += $itemTotal;
                $itemsToSave[] = [
                    'invoice_item_id' => $item->id,
                    'description' => $item->description,
                    'qty' => $returnQty,
                    'unit_price' => $item->unit_price,
                    'total' => $itemTotal,
                ];
            }
        }

        return [$itemsToSave, $subtotal];
    }

    private function updateSalesReturn(SalesReturn $salesReturn, array $data, float $subtotal): void
    {
        $salesReturn->update([
            'customer_id' => $data['customer_id'],
            'invoice_id' => $data['invoice_id'] ?? null,
            'return_date' => $data['return_date'],
            'reason' => $data['reason'],
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'],
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $subtotal,
        ]);
    }

    private function syncItems(SalesReturn $salesReturn, array $items): void
    {
        $salesReturn->items()->delete();
        foreach ($items as $item) {
            $salesReturn->items()->create($item);
        }
    }

    private function handleStatusChange(SalesReturn $salesReturn, Invoice $invoice, SalesReturnStatus $oldStatus, SalesReturnStatus $newStatus): void
    {
        if ($oldStatus !== SalesReturnStatus::Approved && $newStatus === SalesReturnStatus::Approved) {
            $this->salesReturnService->addReturn($invoice, $salesReturn);
        } elseif ($oldStatus === SalesReturnStatus::Approved && $newStatus !== SalesReturnStatus::Approved) {
            $this->salesReturnService->removeReturn($invoice, $salesReturn);
        }
    }
}

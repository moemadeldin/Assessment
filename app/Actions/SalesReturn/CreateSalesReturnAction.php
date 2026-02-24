<?php

declare(strict_types=1);

namespace App\Actions\SalesReturn;

use App\Models\Invoice;
use App\Models\SalesReturn;
use App\Models\User;

final readonly class CreateSalesReturnAction
{
    public function execute(array $data, User $user): SalesReturn
    {
        $invoice = Invoice::with('items')->findOrFail($data['invoice_id']);
        [$itemsToSave, $subtotal] = $this->calculateItemsFromInvoice($data, $invoice);

        $salesReturn = $this->createSalesReturn($data, $user, $subtotal);
        $this->saveItems($salesReturn, $itemsToSave);

        return $salesReturn;
    }

    private function calculateItemsFromInvoice(array $data, Invoice $invoice): array
    {
        $itemsToSave = [];
        $subtotal = 0;

        foreach ($invoice->items as $item) {
            $returnQty = (int) ($data['items'][$item->id]['qty'] ?? 0);
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

    private function createSalesReturn(array $data, User $user, float $subtotal): SalesReturn
    {
        return SalesReturn::query()->create([
            'user_id' => $user->id,
            'customer_id' => $data['customer_id'],
            'invoice_id' => $data['invoice_id'],
            'return_date' => $data['return_date'],
            'reason' => $data['reason'],
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $subtotal,
        ]);
    }

    private function saveItems(SalesReturn $salesReturn, array $items): void
    {
        foreach ($items as $item) {
            $salesReturn->items()->create($item);
        }
    }
}

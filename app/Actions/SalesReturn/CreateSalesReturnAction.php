<?php

declare(strict_types=1);

namespace App\Actions\SalesReturn;

use App\Enums\InvoiceStatus;
use App\Enums\SalesReturnStatus;
use App\Models\Invoice;
use App\Models\SalesReturn;
use App\Models\User;
use App\Services\CalculateInvoiceSalesReturnService;
use Illuminate\Support\Facades\DB;

final readonly class CreateSalesReturnAction
{
    public function __construct(
        private CalculateInvoiceSalesReturnService $salesReturnService,
    ) {}

    public function execute(array $data, User $user): SalesReturn
    {
        return DB::transaction(function () use ($data, $user): SalesReturn {
            $invoice = Invoice::query()
                ->with('items')
                ->where('id', $data['invoice_id'])
                ->lockForUpdate()
                ->findOrFail($data['invoice_id']);
            [$itemsToSave, $subtotal] = $this->calculateItemsFromInvoice($data, $invoice);

            $salesReturn = $this->createSalesReturn($data, $user, $subtotal);
            $this->saveItems($salesReturn, $itemsToSave);

            if ($salesReturn->status === SalesReturnStatus::Approved) {
                $this->salesReturnService->addReturn($invoice, $salesReturn);
                $this->syncInvoiceStatus($invoice->fresh());
            }

            return $salesReturn;
        });
    }

    private function calculateItemsFromInvoice(array $data, Invoice $invoice): array
    {
        $itemsToSave = [];
        $subtotal = 0;

        foreach ($invoice->items as $item) {
            $returnQty = (int) ($data['items'][$item->id]['qty'] ?? 0);
            $returnQty = min($returnQty, $item->qty);

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
            'status' => $data['status'] ?? SalesReturnStatus::Pending->value,
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $subtotal,
        ]);
    }

    private function saveItems(SalesReturn $salesReturn, array $items): void
    {
        $salesReturn->items()->createMany($items);
    }

    private function syncInvoiceStatus(Invoice $invoice): void
    {
        $invoice->loadSum('approvedReturns', 'total');
        $approvedTotal = (float) $invoice->approved_returns_sum_total;

        if ($approvedTotal >= (float) $invoice->total) {
            $invoice->update([
                'pre_return_status' => $invoice->status,
                'status' => InvoiceStatus::Returned->value,
            ]);
        }
    }
}

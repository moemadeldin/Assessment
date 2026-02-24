<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\DTOs\Invoices\InvoiceItemData;
use App\DTOs\Invoices\StoreInvoiceData;
use App\Models\Invoice;
use App\Models\User;
use App\Services\InvoiceItemSyncService;
use Illuminate\Support\Facades\DB;

final readonly class CreateInvoiceAction
{
    public function __construct(
        private InvoiceItemSyncService $invoiceItemSync,
    ) {}

    public function execute(StoreInvoiceData $data, User $user): Invoice
    {
        return DB::transaction(function () use ($data, $user): Invoice {
            $invoice = Invoice::query()->create([
                'customer_id' => $data->customerId,
                'user_id' => $user->id,
                'status' => $data->status,
                'invoice_date' => $data->invoiceDate,
                'due_date' => $data->dueDate,
                'notes' => $data->notes,
            ]);
            $this->createMany($data, $invoice);
            $this->invoiceItemSync->applyTotals($invoice, $data->items, $data->taxRate);

            return $invoice;
        });
    }

    private function createMany(StoreInvoiceData $data, Invoice $invoice): void
    {
        $invoice->items()->createMany(
            array_map(
                static fn (InvoiceItemData $item): array => [
                    'description' => $item->description,
                    'qty' => $item->qty,
                    'unit_price' => $item->unitPrice,
                    'total' => $item->total(),
                ],
                $data->items,
            ),
        );
    }
}

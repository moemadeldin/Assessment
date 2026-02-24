<?php

declare(strict_types=1);

namespace App\Actions\SalesReturn;

use App\Enums\SalesReturnStatus;
use App\Models\Invoice;
use App\Models\SalesReturn;
use App\Services\CalculateInvoiceSalesReturnService;

final readonly class UpdateSalesReturnStatusAction
{
    public function __construct(
        private CalculateInvoiceSalesReturnService $salesReturnService,
    ) {}

    public function execute(SalesReturn $salesReturn, SalesReturnStatus $newStatus): SalesReturn
    {
        $oldStatus = $salesReturn->status;
        $salesReturn->update(['status' => $newStatus->value]);

        if ($salesReturn->invoice_id && $salesReturn->invoice) {
            $this->handleInvoiceUpdate($salesReturn->invoice, $oldStatus, $newStatus, $salesReturn);
        }

        return $salesReturn->refresh();
    }

    private function handleInvoiceUpdate(Invoice $invoice, SalesReturnStatus $oldStatus, SalesReturnStatus $newStatus, SalesReturn $salesReturn): void
    {
        if ($oldStatus !== SalesReturnStatus::Approved && $newStatus === SalesReturnStatus::Approved) {
            $this->salesReturnService->addReturn($invoice, $salesReturn);
        } elseif ($oldStatus === SalesReturnStatus::Approved && $newStatus !== SalesReturnStatus::Approved) {
            $this->salesReturnService->removeReturn($invoice, $salesReturn);
        }
    }
}

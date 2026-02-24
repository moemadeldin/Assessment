<?php

declare(strict_types=1);

namespace App\Actions\SalesReturn;

use App\Enums\SalesReturnStatus;
use App\Models\SalesReturn;
use App\Services\CalculateInvoiceSalesReturnService;

final readonly class DeleteSalesReturnAction
{
    public function __construct(
        private CalculateInvoiceSalesReturnService $salesReturnService,
    ) {}

    public function execute(SalesReturn $salesReturn): void
    {
        if ($salesReturn->invoice_id && $salesReturn->invoice && $salesReturn->status === SalesReturnStatus::Approved) {
            $this->salesReturnService->removeReturn($salesReturn->invoice, $salesReturn);
        }

        $salesReturn->delete();
    }
}

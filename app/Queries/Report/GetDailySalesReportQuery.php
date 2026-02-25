<?php

declare(strict_types=1);

namespace App\Queries\Report;

use App\Enums\SalesReturnStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SalesReturn;
use App\Utils\Constants;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

final readonly class GetDailySalesReportQuery
{
    public function execute(string $date): array
    {
        $allInvoices = $this->getInvoicesForDate($date, false);
        $returns = $this->getReturnsForDate($date);
        $payments = $this->getPaymentsForDate($date);
        $invoices = $this->getInvoicesForDate($date, true);

        $totalSales = $this->calculateTotalSales($allInvoices);
        $totalReturns = $this->calculateTotalReturns($returns);
        $totalPayments = $this->calculateTotalPayments($payments);

        return [
            'invoices' => $invoices,
            'total_sales' => $totalSales,
            'total_returns' => $totalReturns,
            'total_payments' => $totalPayments,
            'net_sales' => $totalSales - $totalReturns,
        ];
    }

    private function getInvoicesForDate(string $date, bool $paginate = false): Paginator|Collection
    {
        $query = Invoice::query()
            ->with('customer')
            ->withSum(['salesReturns as return_total' => function ($query): void {
                $query->where('status', SalesReturnStatus::Approved);
            }], 'total')
            ->whereDate('invoice_date', $date);

        if ($paginate) {
            return $query->simplePaginate(Constants::NUMBER_OF_PAGINATED_REPORTS);
        }

        return $query->get();
    }

    private function getReturnsForDate(string $date): Collection
    {
        return SalesReturn::query()
            ->whereDate('return_date', $date)
            ->where('status', SalesReturnStatus::Approved)
            ->get();
    }

    private function getPaymentsForDate(string $date): Collection
    {
        return Payment::query()
            ->whereDate('payment_date', $date)
            ->get();
    }

    private function calculateTotalSales(Collection $invoices): float
    {
        return (float) $invoices->sum(fn (Invoice $invoice): float => $invoice->total - (float) ($invoice->return_total ?? 0));
    }

    private function calculateTotalReturns(Collection $returns): float
    {
        return (float) $returns->sum('total');
    }

    private function calculateTotalPayments(Collection $payments): float
    {
        return (float) $payments->sum('amount');
    }
}

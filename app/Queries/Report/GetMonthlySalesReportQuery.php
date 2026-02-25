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

final readonly class GetMonthlySalesReportQuery
{
    public function execute(int $year, int $month): array
    {
        $invoices = $this->getInvoicesForPeriod($year, $month, true);

        $totalSales = $this->calculateTotalSales($year, $month);
        $returns = $this->getReturnsForPeriod($year, $month);
        $payments = $this->getPaymentsForPeriod($year, $month);

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

    private function getInvoicesForPeriod(int $year, int $month, bool $paginate = false): Paginator|Collection
    {
        $query = Invoice::query()
            ->with('customer')
            ->withSum(['salesReturns as return_total' => function ($query): void {
                $query->where('status', SalesReturnStatus::Approved);
            }], 'total')
            ->whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month);

        if ($paginate) {
            return $query->simplePaginate(Constants::NUMBER_OF_PAGINATED_REPORTS);
        }

        return $query->get();
    }

    private function calculateTotalSales(int $year, int $month): float
    {
        $invoices = Invoice::query()
            ->whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month)
            ->select('id', 'total')
            ->get();

        if ($invoices->isEmpty()) {
            return 0.0;
        }

        $totalInvoiceAmount = (float) $invoices->sum('total');

        $invoiceIds = $invoices->pluck('id');

        $totalReturns = (float) SalesReturn::query()
            ->whereIn('invoice_id', $invoiceIds)
            ->where('status', SalesReturnStatus::Approved)
            ->sum('total');

        return $totalInvoiceAmount - $totalReturns;
    }

    private function getReturnsForPeriod(int $year, int $month): Collection
    {
        return SalesReturn::query()
            ->whereYear('return_date', $year)
            ->whereMonth('return_date', $month)
            ->where('status', SalesReturnStatus::Approved)
            ->get();
    }

    private function getPaymentsForPeriod(int $year, int $month): Collection
    {
        return Payment::query()
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->get();
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

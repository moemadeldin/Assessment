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
        $invoices = $this->getInvoicesForDate($date, true);

        $totals = $this->calculateAllTotals($date);

        return [
            'invoices' => $invoices,
            'total_sales' => $totals['total_sales'],
            'total_returns' => $totals['total_returns'],
            'total_payments' => $totals['total_payments'],
            'net_sales' => $totals['total_sales'] - $totals['total_returns'],
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

    private function calculateAllTotals(string $date): array
    {
        $invoices = Invoice::query()
            ->whereDate('invoice_date', $date)
            ->select('id', 'total')
            ->get();

        $totalInvoiceAmount = (float) $invoices->sum('total');

        if ($invoices->isEmpty()) {
            return [
                'total_sales' => 0.0,
                'total_returns' => 0.0,
                'total_payments' => 0.0,
            ];
        }

        $invoiceIds = $invoices->pluck('id');

        $totalReturns = (float) SalesReturn::query()
            ->whereIn('invoice_id', $invoiceIds)
            ->where('status', SalesReturnStatus::Approved)
            ->sum('total');

        $totalPayments = (float) Payment::query()
            ->whereDate('payment_date', $date)
            ->sum('amount');

        return [
            'total_sales' => $totalInvoiceAmount,
            'total_returns' => $totalReturns,
            'total_payments' => $totalPayments,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Queries\Report;

use App\Enums\SalesReturnStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SalesReturn;
use Illuminate\Support\Collection;

final readonly class GetProfitReportQuery
{
    public function execute(int $year, int $month): array
    {
        $invoices = $this->getInvoicesForPeriod($year, $month);
        $returns = $this->getReturnsForPeriod($year, $month);
        $payments = $this->getPaymentsForPeriod($year, $month);

        $revenue = $this->calculateRevenue($invoices);
        $returnsTotal = $this->calculateReturnsTotal($returns);
        $paymentsTotal = $this->calculatePaymentsTotal($payments);
        $returnBreakdown = $this->calculateReturnBreakdown($returns);

        return [
            'revenue' => $revenue,
            'returns' => $returnsTotal,
            'payments' => $paymentsTotal,
            'net_revenue' => $revenue - $returnsTotal,
            'return_breakdown' => $returnBreakdown,
        ];
    }

    private function getInvoicesForPeriod(int $year, int $month): Collection
    {
        return Invoice::query()
            ->whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month)
            ->select('id', 'total')
            ->get();
    }

    private function getReturnsForPeriod(int $year, int $month): Collection
    {
        return SalesReturn::query()
            ->whereYear('return_date', $year)
            ->whereMonth('return_date', $month)
            ->where('status', SalesReturnStatus::Approved)
            ->select('id', 'invoice_id', 'total')
            ->get();
    }

    private function getPaymentsForPeriod(int $year, int $month): Collection
    {
        return Payment::query()
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->select('id', 'invoice_id', 'amount')
            ->get();
    }

    private function calculateRevenue(Collection $invoices): float
    {
        return (float) $invoices->sum('total');
    }

    private function calculateReturnsTotal(Collection $returns): float
    {
        return (float) $returns->sum('total');
    }

    private function calculatePaymentsTotal(Collection $payments): float
    {
        return (float) $payments->sum('amount');
    }

    private function calculateReturnBreakdown(Collection $returns): Collection
    {
        return $returns
            ->groupBy('invoice_id')
            ->map(fn (Collection $invoiceReturns, string $invoiceId): array => [
                'invoice_id' => $invoiceId,
                'total' => (float) $invoiceReturns->sum('total'),
                'count' => $invoiceReturns->count(),
            ])
            ->values();
    }
}

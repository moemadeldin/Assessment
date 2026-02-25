<?php

declare(strict_types=1);

namespace App\Queries\Report;

use App\Enums\SalesReturnStatus;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Support\Collection;

final readonly class GetTopCustomersReportQuery
{
    public function execute(int $limit = 10): Collection
    {
        $customers = $this->getCustomersWithRelations();

        return $this->formatCustomerData($customers, $limit);
    }

    private function getCustomersWithRelations(): Collection
    {
        return Customer::query()
            ->with([
                'invoices' => function ($query): void {
                    $query->select('id', 'customer_id', 'total');
                },
                'invoices.payments' => function ($query): void {
                    $query->select('invoice_id', 'amount');
                },
                'invoices.salesReturns' => function ($query): void {
                    $query->where('status', SalesReturnStatus::Approved)
                        ->select('invoice_id', 'total');
                },
            ])
            ->get();
    }

    private function formatCustomerData(Collection $customers, int $limit): Collection
    {
        return $customers
            ->map(function (Customer $customer): array {
                $totalInvoiced = $this->calculateTotalInvoiced($customer);
                $totalPaid = $this->calculateTotalPaid($customer);
                $totalReturns = $this->calculateTotalReturns($customer);

                return [
                    'customer' => $customer,
                    'total_invoiced' => $totalInvoiced,
                    'total_paid' => $totalPaid,
                    'total_returns' => $totalReturns,
                    'balance' => $totalInvoiced - $totalPaid - $totalReturns,
                    'invoice_count' => $customer->invoices->count(),
                ];
            })
            ->sortByDesc('total_invoiced')
            ->take($limit)
            ->values();
    }

    private function calculateTotalInvoiced(Customer $customer): float
    {
        return (float) $customer->invoices->sum('total');
    }

    private function calculateTotalPaid(Customer $customer): float
    {
        return (float) $customer->invoices
            ->flatMap(fn (Invoice $invoice): Collection => $invoice->payments)
            ->sum('amount');
    }

    private function calculateTotalReturns(Customer $customer): float
    {
        return (float) $customer->invoices
            ->flatMap(fn (Invoice $invoice): Collection => $invoice->salesReturns)
            ->sum('total');
    }
}

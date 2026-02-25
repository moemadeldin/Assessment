<?php

declare(strict_types=1);

namespace App\Queries\Customer;

use App\DTOs\CustomerStatement\CustomerStatementData;
use App\DTOs\CustomerStatement\CustomerStatementResult;
use App\DTOs\CustomerStatement\CustomerStatementSummary;
use App\Enums\SalesReturnStatus;
use App\Models\Customer;
use Illuminate\Support\Collection;

final readonly class GetCustomerStatementQuery
{
    public function execute(Customer $customer): CustomerStatementResult
    {
        $customer->load(['invoices.payments', 'invoices.salesReturns', 'salesReturns']);

        $invoices = $this->mapInvoices($customer);
        $payments = $this->mapPayments($customer);
        $returns = $this->mapReturns($customer);

        $transactions = $this->buildTransactions($invoices, $payments, $returns);
        $summary = $this->buildSummary($invoices, $payments, $returns);

        return new CustomerStatementResult(
            transactions: $transactions,
            summary: $summary,
        );
    }

    private function mapInvoices(Customer $customer): Collection
    {
        return $customer->invoices()
            ->oldest('invoice_date')
            ->get()
            ->map(fn ($invoice): CustomerStatementData => new CustomerStatementData(
                type: 'invoice',
                date: $invoice->invoice_date,
                reference: $invoice->invoice_number,
                debit: (float) $invoice->total,
                credit: 0.0,
            ));
    }

    private function mapPayments(Customer $customer): Collection
    {
        return $customer->payments()
            ->oldest('payment_date')
            ->get()
            ->map(fn ($payment): CustomerStatementData => new CustomerStatementData(
                type: 'payment',
                date: $payment->payment_date,
                reference: 'PAY-'.mb_substr((string) $payment->id, 0, 8),
                debit: 0.0,
                credit: (float) $payment->amount,
            ));
    }

    private function mapReturns(Customer $customer): Collection
    {
        return $customer->salesReturns()
            ->where('status', SalesReturnStatus::Approved)
            ->oldest('return_date')
            ->get()
            ->map(fn ($return): CustomerStatementData => new CustomerStatementData(
                type: 'return',
                date: $return->return_date,
                reference: $return->return_number,
                debit: 0.0,
                credit: (float) $return->total,
            ));
    }

    private function buildTransactions(
        Collection $invoices,
        Collection $payments,
        Collection $returns,
    ): Collection {
        $runningBalance = 0.0;

        return $invoices
            ->concat($payments)
            ->concat($returns)
            ->sortBy('date')
            ->values()
            ->map(function (CustomerStatementData $transaction) use (&$runningBalance): CustomerStatementData {
                $runningBalance += $transaction->debit - $transaction->credit;

                return $transaction->withBalance($runningBalance);
            });
    }

    private function buildSummary(
        Collection $invoices,
        Collection $payments,
        Collection $returns,
    ): CustomerStatementSummary {
        return new CustomerStatementSummary(
            totalInvoiced: $invoices->sum('debit'),
            totalPaid: $payments->sum('credit'),
            totalReturns: $returns->sum('credit'),
        );
    }
}

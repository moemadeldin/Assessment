<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payment;

use App\Actions\Payment\CreatePaymentAction;
use App\Actions\Payment\DeletePaymentAction;
use App\Enums\InvoiceStatus;
use App\Http\Requests\Payment\FilterPaymentRequest;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Queries\Payment\GetPaymentsQuery;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final readonly class PaymentController
{
    public function __construct(
        private GetPaymentsQuery $getPaymentsQuery,
    ) {}

    public function index(FilterPaymentRequest $request): View
    {
        $payments = $this->getPaymentsQuery->execute($request->validated());

        return view('payments.index', [
            'payments' => $payments,
            'filters' => $request->validated(),
        ]);
    }

    public function create(Request $request): View
    {
        $invoiceId = $request->query('invoice');

        $invoices = Invoice::query()
            ->withCustomerAndUsers()
            ->whereNotIn('status', [InvoiceStatus::Paid,
                InvoiceStatus::Cancelled,
                InvoiceStatus::Returned, ])
            ->get();

        $selectedInvoice = null;
        if ($invoiceId) {
            $selectedInvoice = Invoice::with(['items', 'customer', 'payments'])
                ->find($invoiceId);

            if ($selectedInvoice) {
                $invoices = $invoices->push($selectedInvoice);
            }
        }

        return view('payments.create', [
            'invoices' => $invoices,
            'selectedInvoice' => $selectedInvoice,
            'invoice_id' => $invoiceId,
        ]);
    }

    public function store(#[CurrentUser()] User $user, StorePaymentRequest $request, CreatePaymentAction $action): RedirectResponse
    {
        $action->execute($request->validated(), $user);

        return to_route('payments.index')->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment): View
    {
        $payment->load(['invoice', 'customer', 'invoice.payments']);

        return view('payments.show', ['payment' => $payment]);
    }

    public function destroy(Payment $payment, DeletePaymentAction $action): RedirectResponse
    {
        $action->execute($payment);

        return to_route('payments.index')->with('success', 'Payment deleted successfully.');
    }
}

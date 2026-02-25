<?php

declare(strict_types=1);

namespace App\Http\Controllers\SalesReturn;

use App\Actions\SalesReturn\CreateSalesReturnAction;
use App\Actions\SalesReturn\DeleteSalesReturnAction;
use App\Actions\SalesReturn\UpdateSalesReturnAction;
use App\Http\Requests\SalesReturn\FilterSalesReturnRequest;
use App\Http\Requests\SalesReturn\StoreSalesReturnRequest;
use App\Http\Requests\SalesReturn\UpdateSalesReturnRequest;
use App\Models\Invoice;
use App\Models\SalesReturn;
use App\Models\User;
use App\Queries\SalesReturn\GetSalesReturnsQuery;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final readonly class SalesReturnController
{
    public function __construct(
        private GetSalesReturnsQuery $getSalesReturnsQuery,
    ) {}

    public function index(FilterSalesReturnRequest $request): View
    {
        $salesReturns = $this->getSalesReturnsQuery->execute($request->validated());

        return view('sales-returns.index', [
            'salesReturns' => $salesReturns,
            'filters' => $request->validated(),
        ]);
    }

    public function create(Invoice $invoice): View
    {

        $invoice->load(['items', 'customer', 'user']);

        return view('sales-returns.create', [
            'invoice' => $invoice,
            'invoiceItems' => $invoice->items,
        ]);
    }

    public function store(#[CurrentUser()] User $user, StoreSalesReturnRequest $request, CreateSalesReturnAction $action): RedirectResponse
    {
        $action->execute($request->validated(), $user);

        return to_route('sales-returns.index')->with('success', 'Sales return created successfully.');
    }

    public function show(SalesReturn $salesReturn): View
    {
        return view('sales-returns.show', ['salesReturn' => $salesReturn]);
    }

    public function edit(SalesReturn $salesReturn): View
    {
        $salesReturn->load('items');

        $invoice = null;
        if ($salesReturn->invoice_id) {
            $invoice = Invoice::with(['items', 'customer', 'user'])->find($salesReturn->invoice_id);
        }

        return view('sales-returns.edit', [
            'salesReturn' => $salesReturn,
            'invoice' => $invoice,
        ]);
    }

    public function update(UpdateSalesReturnRequest $request, SalesReturn $salesReturn, UpdateSalesReturnAction $action): RedirectResponse
    {
        $action->execute($salesReturn, $request->validated());

        return to_route('sales-returns.show', $salesReturn)->with('success', 'Sales return updated successfully.');
    }

    public function destroy(SalesReturn $salesReturn, DeleteSalesReturnAction $action): RedirectResponse
    {
        $action->execute($salesReturn);

        return to_route('sales-returns.index')->with('success', 'Sales return deleted successfully.');
    }
}

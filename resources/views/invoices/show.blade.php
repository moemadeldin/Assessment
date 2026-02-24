@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
@php
    use App\Enums\InvoiceStatus;
@endphp
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-slate-800 rounded-2xl p-8 shadow-2xl">
            <h1 class="text-2xl font-semibold text-slate-50 mb-1">Invoice</h1>
            <p class="text-slate-400 mb-6">{{ $invoice->invoice_number }}</p>

            <div class="space-y-5">
                <div>
                    <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-1">Customer</label>
                    <p class="text-slate-50 text-base">{{ $invoice->customer->name }} ({{ $invoice->customer->email }})</p>
                </div>

                <div>
                    <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-1">Status</label>
                    <p>
                        <span class="px-3 py-1 text-sm rounded {{ $invoice->status->badgeClass() }}">
                            {{ $invoice->status->label() }}
                        </span>
                    </p>
                </div>

                <div>
                    <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-1">Due Date</label>
                    <p class="text-slate-50 text-base">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '-' }}
                    </p>
                </div>
            </div>

            <div class="mt-6 bg-slate-900 rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-700">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-200">Description</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-200">Qty</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-200">Unit Price</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-200">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @foreach($invoice->items as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-slate-200">{{ $item->description }}</td>
                                <td class="px-4 py-3 text-sm text-slate-200">{{ $item->qty }}</td>
                                <td class="px-4 py-3 text-sm text-slate-200">${{ $item->formatted_unit_price }}</td>
                                <td class="px-4 py-3 text-sm text-slate-200">${{ $item->formatted_total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 text-right space-y-2">
                <p class="text-slate-200">Subtotal: ${{ $invoice->formatted_subtotal }}</p>
                <p class="text-slate-200">Tax: ${{ $invoice->formatted_tax }}</p>
                @if($invoice->sales_return_total > 0)
                    <p class="text-red-400">Returns: -${{ $invoice->formatted_sales_return_total }}</p>
                    <p class="text-green-400 text-xl font-semibold">Adjusted Total: ${{ $invoice->formatted_adjusted_total }}</p>
                @else
                    <p class="text-green-400 text-xl font-semibold">Total: ${{ $invoice->formatted_total }}</p>
                @endif
            </div>

            @if($invoice->notes)
                <div class="mt-6">
                    <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-1">Notes</label>
                    <p class="text-slate-50 text-base">{{ $invoice->notes }}</p>
                </div>
            @endif

            <div class="flex gap-4 mt-8">
                <a href="{{ route('invoices.index') }}"
                    class="px-6 py-3 rounded-lg text-white font-semibold text-sm gradient-primary hover:shadow-lg transition-all">
                    Back to Invoices
                </a>
                <a href="{{ route('invoices.edit', $invoice) }}"
                    class="px-6 py-3 rounded-lg text-white font-semibold text-sm bg-slate-700 hover:bg-slate-600 transition-all">
                    Edit
                </a>
                <a href="{{ route('invoices.print', $invoice) }}" target="_blank"
                    class="px-6 py-3 rounded-lg text-white font-semibold text-sm bg-slate-600 hover:bg-slate-500 transition-all">
                    Print
                </a>
                @if($invoice->status === InvoiceStatus::Paid)
                        <a href="{{ route('sales-returns.create', $invoice->id) }}"
                            class="px-6 py-3 rounded-lg text-white font-semibold text-sm bg-amber-600 hover:bg-amber-500 transition-all"
                            title="Record Return">
                            Return
                        </a>
                        
                @endif
                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="cursor-pointer px-6 py-3 rounded-lg text-white font-semibold text-sm bg-red-600 hover:bg-red-500 transition-all"
                        onclick="return confirm('Are you sure?')">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

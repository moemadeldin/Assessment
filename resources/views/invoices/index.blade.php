@extends('layouts.app')

@section('title', 'Invoices')

@section('content')

@php
    use App\Enums\InvoiceStatus;
@endphp
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-semibold text-slate-50">Invoices</h1>
            <a href="{{ route('invoices.create') }}"
                class="px-6 py-3 rounded-lg text-white font-semibold text-sm gradient-primary hover:shadow-lg hover:-translate-y-0.5 transition-all">
                Create Invoice
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/30 text-green-400 rounded-lg px-4 py-3 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter Form -->
        <div class="bg-slate-800 rounded-2xl p-6 mb-6 shadow-lg">
            <form method="GET" action="{{ route('invoices.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-2">Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Invoice # or client name..."
                        class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder-slate-600">
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-2">Status</label>
                    <select name="status"
                        class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                        <option value="">All Statuses</option>
                        @foreach(InvoiceStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                        class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                        class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="cursor-pointer px-6 py-2 rounded-lg text-white font-semibold text-sm gradient-primary hover:shadow-lg transition-all">
                        Filter
                    </button>
                    <a href="{{ route('invoices.index') }}"
                        class="px-6 py-2 rounded-lg text-white font-semibold text-sm bg-slate-700 hover:bg-slate-600 transition-all">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-slate-800 rounded-2xl overflow-hidden shadow-lg">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-700">
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-200">Invoice #</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-200">Client</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-200">Total</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-200">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-200">Invoice Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-200">Due Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-200">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-200">{{ $invoice->invoice_number }}</td>
                            <td class="px-6 py-4 text-sm text-slate-200">{{ $invoice->customer->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-200">
                                @if($invoice->sales_return_total > 0)
                                    <span class="text-red-400 line-through">${{ $invoice->formatted_total }}</span>
                                    <br>
                                    <span class="text-green-400">${{ $invoice->formatted_adjusted_total }}</span>
                                @else
                                    ${{ $invoice->formatted_total }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 text-sm rounded {{ $invoice->status->badgeClass() }}">
                                    {{ $invoice->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-200">
                                {{ $invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-200">
                                {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('invoices.show', $invoice) }}"
                                        class="px-4 py-2 rounded-lg text-white font-semibold text-xs gradient-primary hover:shadow-lg transition-all">
                                        View
                                    </a>
                                    @if($invoice->status === InvoiceStatus::Paid)
                                        <a href="{{ route('sales-returns.create', $invoice->id) }}"
                                            class="px-4 py-2 rounded-lg text-white font-semibold text-xs bg-amber-600 hover:bg-amber-500 transition-all"
                                            title="Record Return">
                                            Return
                                        </a>
                                    @endif
                                    @if(! in_array($invoice->status, [
                                            InvoiceStatus::Paid,
                                            InvoiceStatus::Returned,
                                            InvoiceStatus::Cancelled
                                        ]))
                                        <a href="{{ route('payments.create', ['invoice' => $invoice->id]) }}"
                                            class="px-4 py-2 rounded-lg text-white font-semibold text-xs bg-blue-600 hover:bg-blue-500 transition-all"
                                            title="Record Payment">
                                            Pay
                                        </a>
                                    @endif
                                    <a href="{{ route('invoices.edit', $invoice) }}"
                                        class="px-4 py-2 rounded-lg text-white font-semibold text-xs bg-slate-700 hover:bg-slate-600 transition-all">
                                        Edit
                                    </a>
                                    <a href="{{ route('invoices.download', $invoice) }}"
                                        class="px-4 py-2 rounded-lg text-white font-semibold text-xs bg-green-600 hover:bg-green-500 transition-all"
                                        title="Download PDF">
                                        PDF
                                    </a>
                                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="cursor-pointer px-4 py-2 rounded-lg text-white font-semibold text-xs bg-gradient-to-r from-red-500 to-red-600 hover:shadow-lg transition-all"
                                            onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container mt-4">
            {{ $invoices->links() }}
        </div>
    </div>
@endsection
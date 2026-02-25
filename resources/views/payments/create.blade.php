@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-slate-50">Record Payment</h1>
            <a href="{{ route('payments.index') }}"
                class="px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-slate-200 transition">
                Back
            </a>
        </div>

        <div class="bg-slate-800 rounded-2xl p-8 shadow-lg">
            <form method="POST" action="{{ route('payments.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="invoice_id" class="block text-slate-300 text-sm font-medium mb-2">Invoice</label>
                    <select name="invoice_id" id="invoice_id" required
                        class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                        <option value="">Select Invoice</option>
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" {{ $invoice_id && $invoice_id === (string)$invoice->id ? 'selected' : '' }}>
                                {{ $invoice->invoice_number }} - {{ $invoice->customer->name }} -
                                ${{ number_format((float) $invoice->total, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('invoice_id')
                        <div class="mt-1 text-sm text-red-400">{{ $message }}</div>
                    @enderror
                </div>

                @if($selectedInvoice)
                    <div class="mb-6 p-4 bg-slate-900 rounded-lg">
                        <h3 class="text-sm font-medium text-slate-300 mb-2">Invoice Details</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-slate-400">Total:</span>
                                <span class="text-slate-50 ml-2">${{ number_format((float) $selectedInvoice->total, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400">Paid:</span>
                                <span class="text-green-400 ml-2">${{ number_format($selectedInvoice->paid_amount, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400">Remaining:</span>
                                <span
                                    class="text-red-400 ml-2">${{ number_format($selectedInvoice->remaining_amount, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400">Status:</span>
                                <span class="px-2 py-0.5 text-xs rounded {{ $selectedInvoice->payment_status->badgeClass() }}">
                                    {{ $selectedInvoice->payment_status->label() }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="amount" class="block text-slate-300 text-sm font-medium mb-2">Amount</label>
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" required
                            class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                        @error('amount')
                            <div class="mt-1 text-sm text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_date" class="block text-slate-300 text-sm font-medium mb-2">Payment Date</label>
                        <input type="date" name="payment_date" id="payment_date"
                            value="{{ old('payment_date', date('Y-m-d')) }}" required
                            class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="payment_method" class="block text-slate-300 text-sm font-medium mb-2">Payment
                            Method</label>
                        <select name="payment_method" id="payment_method" required
                            class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                            @foreach(\App\Enums\PaymentMethod::cases() as $method)
                                <option value="{{ $method->value }}">{{ $method->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="reference_number" class="block text-slate-300 text-sm font-medium mb-2">Reference Number
                            (Optional)</label>
                        <input type="text" name="reference_number" id="reference_number"
                            value="{{ old('reference_number') }}"
                            class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-slate-300 text-sm font-medium mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                        class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">{{ old('notes') }}</textarea>
                </div>

                <button type="submit"
                    class="cursor-pointer w-full py-3 rounded-lg text-white font-semibold text-sm gradient-primary hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    Record Payment
                </button>
            </form>
        </div>
    </div>
@endsection
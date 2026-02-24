@extends('layouts.app')

@section('title', 'Edit Sales Return')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-slate-50">Edit Sales Return</h1>
            <a href="{{ route('sales-returns.index') }}"
                class="px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-slate-200 transition">
                Back
            </a>
        </div>

        <div class="bg-slate-800 rounded-2xl p-8 shadow-lg">
            @if($invoice)
            <div class="mb-6 p-4 bg-slate-900 rounded-lg">
                <h2 class="text-lg font-medium text-slate-50 mb-3">Invoice Details</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-slate-400">Invoice #:</span>
                        <span class="text-slate-50 ml-2">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400">Customer:</span>
                        <span class="text-slate-50 ml-2">{{ $invoice->customer->name }}</span>
                    </div>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('sales-returns.update', $salesReturn) }}" class="space-y-6">
                @csrf
                @method('PUT')

                @if($invoice)
                <input type="hidden" name="customer_id" value="{{ $invoice->customer_id }}">
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                @else
                <input type="hidden" name="customer_id" value="{{ $salesReturn->customer_id }}">
                <input type="hidden" name="invoice_id" value="">
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="return_date" class="block text-slate-300 text-sm font-medium mb-2">Return Date</label>
                        <input type="date" name="return_date" id="return_date"
                            value="{{ old('return_date', $salesReturn->return_date->format('Y-m-d')) }}" required
                            class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                    </div>

                    <div>
                        <label for="status" class="block text-slate-300 text-sm font-medium mb-2">Status</label>
                        <select name="status" id="status"
                            class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                            @foreach(\App\Enums\SalesReturnStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ old('status', $salesReturn->status?->value) === $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="reason" class="block text-slate-300 text-sm font-medium mb-2">Reason</label>
                    <input type="text" name="reason" id="reason" value="{{ old('reason', $salesReturn->reason) }}" required
                        class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <div>
                    <label for="notes" class="block text-slate-300 text-sm font-medium mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                        class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">{{ old('notes', $salesReturn->notes) }}</textarea>
                </div>

                @if($invoiceItems && $invoiceItems->count() > 0)
                    <div>
                        <h3 class="text-lg font-medium mb-3 text-slate-50">Items to Return</h3>
                        <p class="text-sm text-slate-400 mb-3">Enter the qty you want to return for each item.</p>
                        <div class="space-y-3">
                            @foreach($invoiceItems as $item)
                                @php
                                    $returnItem = $salesReturn->items->firstWhere('invoice_item_id', $item->id);
                                    $returnQty = $returnItem ? $returnItem->qty : 0;
                                @endphp
                                <div class="grid grid-cols-12 gap-3 items-center bg-slate-900 p-3 rounded-lg">
                                    <div class="col-span-4">
                                        <span class="text-slate-50 text-sm">{{ $item->description }}</span>
                                        <input type="hidden" name="items[{{ $item->id }}][invoice_item_id]" value="{{ $item->id }}">
                                    </div>
                                    <div class="col-span-2 text-center">
                                        <span class="text-slate-400 text-xs">Original: {{ $item->qty }}</span>
                                    </div>
                                    <div class="col-span-3">
                                        <input type="number" name="items[{{ $item->id }}][qty]" 
                                            value="{{ old('items.' . $item->id . '.qty', $returnQty) }}" min="0" max="{{ $item->qty }}"
                                            placeholder="Return Qty"
                                            class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-slate-50 text-sm text-center">
                                    </div>
                                    <div class="col-span-2 text-right">
                                        <span class="text-slate-400 text-sm">${{ number_format((float) $item->unit_price, 2) }}</span>
                                        <input type="hidden" name="items[{{ $item->id }}][unit_price]" value="{{ $item->unit_price }}">
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <span class="text-slate-400 text-sm">
                                            @if($returnQty > 0)
                                                ${{ number_format($returnQty * (float) $item->unit_price, 2) }}
                                            @else
                                                $0.00
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <button type="submit"
                    class="w-full py-3 rounded-lg text-white font-semibold text-sm gradient-primary hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    Update Sales Return
                </button>
            </form>
        </div>
    </div>
@endsection

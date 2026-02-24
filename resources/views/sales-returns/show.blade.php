@extends('layouts.app')

@section('title', 'Sales Return Details')

@section('content')
    @php
        use App\Enums\SalesReturnStatus;
    @endphp
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-slate-50">Sales Return Details</h1>
            <div class="flex gap-2">
                <a href="{{ route('sales-returns.edit', $salesReturn) }}"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-slate-200 transition">
                    Edit
                </a>
                <a href="{{ route('sales-returns.index') }}"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-slate-200 transition">
                    Back
                </a>
            </div>
        </div>

        <div class="bg-slate-800 rounded-2xl p-8 shadow-lg mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-50">{{ $salesReturn->return_number }}</h2>
                    <p class="text-slate-400">{{ $salesReturn->customer->name }}</p>
                    <p class="text-slate-400">{{ $salesReturn->customer->email }}</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="px-3 py-1 text-sm rounded {{ $salesReturn->status->badgeClass() }}">
                        {{ $salesReturn->status->label() }}
                    </span>
                    @if($salesReturn->status === SalesReturnStatus::Pending)
                        <form action="{{ route('sales-returns.update', $salesReturn) }}" method="POST" class="flex gap-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit"
                                class="px-3 py-1 text-xs bg-green-600 hover:bg-green-500 rounded text-white">Approve</button>
                        </form>
                    @elseif($salesReturn->status === SalesReturnStatus::Rejected)
                        <form action="{{ route('sales-returns.update', $salesReturn) }}" method="POST" class="flex gap-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit"
                                class="px-3 py-1 text-xs bg-green-600 hover:bg-green-500 rounded text-white">Approve</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-1">Return Date</label>
                    <p class="text-slate-50">{{ $salesReturn->return_date->format('Y-m-d') }}</p>
                </div>
                @if($salesReturn->invoice)
                    <div>
                        <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-1">Related
                            Invoice</label>
                        <p class="text-slate-50">{{ $salesReturn->invoice->invoice_number }}</p>
                    </div>
                @endif
                <div>
                    <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-1">Total</label>
                    <p class="text-slate-50 text-xl font-bold">${{ $salesReturn->formatted_total }}</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-1">Reason</label>
                <p class="text-slate-50">{{ $salesReturn->reason }}</p>
            </div>

            @if($salesReturn->notes)
                <div class="mb-6">
                    <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-1">Notes</label>
                    <p class="text-slate-50">{{ $salesReturn->notes }}</p>
                </div>
            @endif
        </div>

        <div class="bg-slate-800 rounded-2xl p-8 shadow-lg">
            <h3 class="text-lg font-semibold text-slate-50 mb-4">Return Items</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-400 uppercase bg-slate-900">
                        <tr>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3 text-right">Qty</th>
                            <th class="px-4 py-3 text-right">Unit Price</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesReturn->items as $item)
                            <tr class="border-b border-slate-700">
                                <td class="px-4 py-3 text-slate-50">{{ $item->description }}</td>
                                <td class="px-4 py-3 text-right text-slate-50">{{ $item->qty }}</td>
                                <td class="px-4 py-3 text-right text-slate-50">${{ $item->formatted_unit_price }}</td>
                                <td class="px-4 py-3 text-right text-slate-50">${{ $item->formatted_total }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-center text-slate-400">No items found</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-900">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right font-semibold text-slate-50">Subtotal</td>
                            <td class="px-4 py-3 text-right text-slate-50">${{ $salesReturn->formatted_subtotal }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right font-semibold text-slate-50">Tax</td>
                            <td class="px-4 py-3 text-right text-slate-50">${{ $salesReturn->formatted_tax }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right font-bold text-slate-50">Total</td>
                            <td class="px-4 py-3 text-right font-bold text-slate-50">${{ $salesReturn->formatted_total }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <form action="{{ route('sales-returns.destroy', $salesReturn) }}" method="POST">
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
@endsection
@extends('layouts.app')

@section('title', 'Sales Returns')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-semibold text-slate-50">Sales Returns</h1>
        </div>

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/30 text-green-400 rounded-lg px-4 py-3 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-slate-800 rounded-2xl p-6 mb-6 shadow-lg">
            <form method="GET" action="{{ route('sales-returns.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-2">Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Return # or customer name..."
                        class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                </div>
                <div>
                    <label class="block text-slate-400 text-xs font-medium uppercase tracking-wide mb-2">Status</label>
                    <select name="status"
                        class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-50 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                        <option value="">All Statuses</option>
                        @foreach(\App\Enums\SalesReturnStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="cursor-pointer px-6 py-2 rounded-lg text-white font-semibold text-sm gradient-primary hover:shadow-lg transition-all">
                        Filter
                    </button>
                    <a href="{{ route('sales-returns.index') }}"
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
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-200">Return #</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-200">Customer</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-200">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-200">Total</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-200">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-200">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($salesReturns as $return)
                        <tr class="hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-200">{{ $return->return_number }}</td>
                            <td class="px-6 py-4 text-sm text-slate-200">{{ $return->customer->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-200">{{ $return->return_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-sm text-slate-200">${{ $return->formatted_total }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-sm rounded {{ $return->status->badgeClass() }}">
                                    {{ $return->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('sales-returns.show', $return) }}"
                                        class="px-4 py-2 rounded-lg text-white font-semibold text-xs gradient-primary hover:shadow-lg transition-all">
                                        View
                                    </a>
                                    <form action="{{ route('sales-returns.destroy', $return) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="cursor-pointer px-4 py-2 rounded-lg text-white font-semibold text-xs bg-red-600 hover:bg-red-500 transition-all"
                                            onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">No sales returns found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $salesReturns->links() }}
        </div>
    </div>
@endsection

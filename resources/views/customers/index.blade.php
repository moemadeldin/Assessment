<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-gray-900 text-gray-100">

    <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Customers</h1>
        <div class="flex gap-4">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition">Logout</button>
            </form>
        </div>
    </nav>

    <div class="p-6">
        <div class="max-w-6xl mx-auto">
            @if(session('success'))
                <div class="mb-4 p-3 rounded-md bg-green-900 text-green-300 border border-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <form method="GET" action="{{ route('customers.index') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customers..." 
                           class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition">Search</button>
                </form>
                <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg transition">Add Customer</a>
            </div>

            <div class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($customers as $customer)
                            <tr>
                                <td class="px-6 py-4">{{ $customer->name }}</td>
                                <td class="px-6 py-4">{{ $customer->email }}</td>
                                <td class="px-6 py-4">{{ $customer->phone ?? '-' }}</td>
                                <td class="px-6 py-4 flex gap-2">
                                    <a href="{{ route('customers.show', $customer) }}" class="text-blue-400 hover:text-blue-300">View</a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="text-yellow-400 hover:text-yellow-300">Edit</a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Are you sure?')">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-400">No customers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $customers->links() }}
            </div>
        </div>
    </div>

</body>

</html>

<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Details</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-gray-900 text-gray-100">

    <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Customer Details</h1>
        <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition">Back</a>
    </nav>

    <div class="p-6">
        <div class="max-w-2xl mx-auto">
            @if(session('success'))
                <div class="mb-4 p-3 rounded-md bg-green-900 text-green-300 border border-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-gray-800 p-8 rounded-xl shadow-2xl">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Name</label>
                        <p class="text-lg">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Email</label>
                        <p class="text-lg">{{ $customer->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Phone</label>
                        <p class="text-lg">{{ $customer->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Address</label>
                        <p class="text-lg">{{ $customer->address ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Created At</label>
                        <p class="text-lg">{{ $customer->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex gap-4">
                    <a href="{{ route('customers.edit', $customer) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 rounded-lg transition">Edit</a>
                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Are you sure?')">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

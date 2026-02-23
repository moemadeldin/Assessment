<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-gray-900 text-gray-100">

    <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Edit Customer</h1>
        <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition">Back</a>
    </nav>

    <div class="p-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-gray-800 p-8 rounded-xl shadow-2xl">
                <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-300">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" required 
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('name') <div class="mt-1 text-sm text-red-400">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-300">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" required 
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email') <div class="mt-1 text-sm text-red-400">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block mb-2 text-sm font-medium text-gray-300">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" 
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="address" class="block mb-2 text-sm font-medium text-gray-300">Address</label>
                        <textarea name="address" id="address" rows="3" 
                                  class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address', $customer->address) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 transition duration-200 font-semibold text-white">
                        Update Customer
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>

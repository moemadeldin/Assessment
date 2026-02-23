<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-900 text-gray-100">

    <div class="w-full max-w-md bg-gray-800 p-8 rounded-xl shadow-2xl">

        <h1 class="text-2xl font-bold text-center mb-6 text-white">
            Register
        </h1>

        @if(session('success'))
            <div class="mb-4 p-3 rounded-md bg-green-900 text-green-300 border border-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-300">
                    Name
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg 
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                           text-white placeholder-gray-400">
                @error('name')
                    <div class="mt-1 text-sm text-red-400">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-300">
                    Email
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg 
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                           text-white placeholder-gray-400">
                @error('email')
                    <div class="mt-1 text-sm text-red-400">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-300">
                    Password
                </label>
                <input type="password" name="password" id="password" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg 
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                           text-white placeholder-gray-400">
                @error('password')
                    <div class="mt-1 text-sm text-red-400">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-300">
                    Confirm Password
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg 
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                           text-white placeholder-gray-400">
            </div>

            <button type="submit" class="w-full py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 
                       transition duration-200 font-semibold text-white">
                Register
            </button>

        </form>
    </div>

</body>

</html>
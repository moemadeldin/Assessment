<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-gray-900 text-gray-100">

    <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">
            <a href="{{ route('dashboard') }}">
                Dashboard
            </a>
        </h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="cursor-pointer px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition duration-200">
                Logout
            </button>
        </form>
    </nav>

    <div class="p-6">
        <div class="max-w-4xl mx-auto bg-gray-800 p-8 rounded-xl shadow-2xl">
            <h2 class="text-2xl font-bold mb-4">Welcome, {{ Auth::user()->name }}!</h2>
            <p class="text-gray-400 mb-6">You are now logged in.</p>
            <a href="{{ route('customers.index') }}" class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition">Manage Customers</a>
        </div>
    </div>

</body>

</html>
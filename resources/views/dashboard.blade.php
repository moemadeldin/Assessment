<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300">

    <div class="bg-blue-600 dark:bg-gray-800 text-white px-6 py-4 flex justify-between items-center shadow-md">
        <h1 class="text-xl font-semibold">
            Dashboard
        </h1>
    </div>

    <div class="p-8">
        <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 transition-all duration-300">

            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
                Welcome, {{ Auth::user()->name }}!
            </h2>

            <p class="text-gray-600 dark:text-gray-300">
                You are now logged in.
            </p>

        </div>
    </div>

</body>

</html>
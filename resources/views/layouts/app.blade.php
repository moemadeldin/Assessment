<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Dashboard'))</title>
    @vite('resources/css/app.css')
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        slate: {
                            50: '#f8fafc',
                            200: '#e2e8f0',
                            400: '#94a3b8',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }

        /* Pagination styles */
        .pagination-container nav {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination-container svg {
            width: 20px;
            height: 20px;
        }

        .pagination-container nav>div:last-child {
            display: flex;
            gap: 5px;
        }

        .pagination-container nav>div:first-child {
            display: none;
        }

        .pagination-container nav>div>span>a,
        .pagination-container nav>div>span>span {
            padding: 0.5rem 1rem;
            background-color: #1e293b;
            border: 1px solid #334155;
            color: #e2e8f0;
            text-decoration: none;
            border-radius: 0.25rem;
        }

        .pagination-container nav>div>span>span[aria-current="page"] {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-color: transparent;
        }
    </style>
</head>

<body class="bg-slate-900 min-h-screen font-sans antialiased">
    @auth
        <!-- Navigation -->
        <nav class="bg-slate-800 border-b border-slate-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-slate-50 text-xl font-semibold">
                                Dashboard
                            </a>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('customers.index') }}"
                                class="inline-flex items-center px-1 pt-1 text-sm font-medium text-slate-200 hover:text-slate-50 border-b-2 border-transparent hover:border-indigo-500 transition-colors">
                                Customers
                            </a>
                            <a href="{{ route('invoices.index') }}"
                                class="inline-flex items-center px-1 pt-1 text-sm font-medium text-slate-200 hover:text-slate-50 border-b-2 border-transparent hover:border-indigo-500 transition-colors">
                                Invoices
                            </a>
                            <a href="{{ route('sales-returns.index') }}"
                                class="inline-flex items-center px-1 pt-1 text-sm font-medium text-slate-200 hover:text-slate-50 border-b-2 border-transparent hover:border-indigo-500 transition-colors">
                                Sales Returns
                            </a>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center">
                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <span class="text-slate-200 text-sm mr-4">{{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="cursor-pointer bg-slate-700 text-slate-200 hover:bg-slate-600 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    @endauth

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
</body>

</html>
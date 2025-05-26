<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Flash Messages -->
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Responsive Header -->
    <header class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img class="h-8 w-auto" src="https://via.placeholder.com/150x50?text=Logo" alt="Logo">
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button"
                        class="mobile-menu-button bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars"></i>
                    </button>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <div class="">
                        <ul class="flex flex-wrap -mb-px">
                            <li class="mr-2">
                                <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('home') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                    href="{{ route('dashboard') }}">Home</a>
                            </li>
                            @if (!request()->has('references'))
                                <li class="mr-2">
                                    <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('data/x') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                        href="{{ route('data.index', ['type' => 'x']) }}">Data X</a>
                                </li>
                            @else
                                <li class="mr-2">
                                    <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('data/x') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                        href="{{ route('data.index', ['type' => 'x', 'reference' => request()->query('references')]) }}">Data
                                        X</a>
                                </li>
                            @endif
                            @if (!request()->has('references'))
                                <li class="mr-2">
                                    <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('data/y') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                        href="{{ route('data.index', ['type' => 'y']) }}">Data Y</a>
                                </li>
                            @else
                                <li class="mr-2">
                                    <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('data/y') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                        href="{{ route('data.index', ['type' => 'y', 'reference' => request()->query('references')]) }}">Data
                                        Y</a>
                                </li>
                            @endif
                            @if (!request()->has('references'))
                                <li class="mr-2">
                                    <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('analysis') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                        href="{{ route('analysis') }}">Hasil</a>
                                </li>
                            @else
                                <li class="mr-2">
                                    <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('analysis') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                        href="{{ route('analysis', ['reference' => request()->query('references')]) }}">Hasil</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </nav>

                <!-- Login Button -->
                @if (!auth()->check())
                    <div class="hidden md:block">
                        <a href="{{ route('login') }}"
                            class="ml-8 whitespace-nowrap inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md  text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            Masuk
                        </a>
                    </div>
                @else
                    <div class="hidden md:block">
                        <a href="{{ route('login') }}"
                            class="ml-8 whitespace-nowrap inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md  text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            Logout
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="mobile-menu hidden md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('dashboard') }}"
                    class="{{ request()->is('home') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Home</a>

                @if (!request()->has('references'))
                    <a href="{{ route('data.index') }}"
                        class="{{ request()->is('data/x') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Data
                        X</a>
                @else
                    <a href="{{ route('data.index', ['type' => 'x', 'reference' => request()->query('references')]) }}"
                        class="{{ request()->is('data/x') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Data
                        X</a>
                @endif

                @if (!request()->has('references'))
                    <a href="{{ route('data.index', ['type' => 'y']) }}"
                        class="{{ request()->is('data/y') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Data
                        Y</a>
                @else
                    <a href="{{ route('data.index', ['type' => 'y', 'reference' => request()->query('references')]) }}"
                        class="{{ request()->is('data/y') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Data
                        Y</a>
                @endif

                @if (!request()->has('references'))
                    <a href="{{ route('analysis') }}"
                        class="{{ request()->is('analysis') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Hasil</a>
                @else
                    <a href="{{ route('analysis', request()->query('references')) }}"
                        class="{{ request()->is('analysis') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Hasil</a>
                @endif

                <a href="#"
                    class="text-gray-700 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">Masuk</a>
            </div>
        </div>
    </header>

    @yield('content')
    <script src="https://unpkg.com/@heroicons/react@1.0.5/outline.js" crossorigin></script>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-button').addEventListener('click', function() {
            document.querySelector('.mobile-menu').classList.toggle('hidden');
        });
    </script>

    @yield('scripts')
</body>

</html>

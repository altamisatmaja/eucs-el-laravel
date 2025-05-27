<header class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}">
                    <h2 class="text-xl font-bold text-gray-900">EUCS</h2>
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
                            <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('/') || request()->is('dashboard') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                href="{{ route('dashboard', ['references' => session('existingRecordId')]) }}">Home</a>
                        </li>
                        @if (!request()->has('references'))
                            <li class="mr-2">
                                <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('data/analysis/x') || request()->is('data/x/*') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                    href="{{ route('data.index', ['type' => 'x', 'references' => session('existingRecordId')]) }}">
                                    Data X
                                </a>
                            </li>
                        @else
                            <li class="mr-2">
                                <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('data/analysis/x') || request()->is('data/x/*') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                    href="{{ route('data.index', ['type' => 'x', 'references' => request()->query('references')]) }}">Data
                                    X</a>
                            </li>
                        @endif
                        @if (!request()->has('references'))
                            <li class="mr-2">
                                <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('data/analysis/y') || request()->is('data/y/*') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                    href="{{ route('data.index', ['type' => 'y', 'references' => session('existingRecordId')]) }}">Data
                                    Y</a>
                            </li>
                        @else
                            <li class="mr-2">
                                <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('data/analysis/y') || request()->is('data/y/*') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                    href="{{ route('data.index', ['type' => 'y', 'references' => request()->query('references')]) }}">Data
                                    Y</a>
                            </li>
                        @endif
                        @if (!request()->has('references'))
                            <li class="mr-2">
                                <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('analysis') || request()->is('analysis/*') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                    href="{{ route('analysis', ['references' => session('existingRecordId')]) }}">Hasil</a>
                            </li>
                        @else
                            <li class="mr-2">
                                <a class="inline-block p-4 border-b-2 font-medium text-sm {{ request()->is('analysis') || request()->is('analysis/*') ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                    href="{{ route('analysis', ['references' => request()->query('references')]) }}">Hasil</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Login Button -->
            @if (!auth()->check())
                <div class="hidden md:block">
                    <a href="{{ route('login') }}"
                        class="ml-8 whitespace-nowrap inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Masuk
                    </a>
                </div>
            @else
                <div class="hidden md:block">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="ml-8 whitespace-nowrap inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            Logout
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="mobile-menu hidden md:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <a href="{{ route('dashboard') }}"
                class="{{ request()->is('/') || request()->is('dashboard') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Home</a>

            @if (!request()->has('references'))
                <a href="{{ route('data.index', ['type' => 'x']) }}"
                    class="{{ request()->is('data/x') || request()->is('data/x/*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Data
                    X</a>
            @else
                <a href="{{ route('data.index', ['type' => 'x', 'reference' => request()->query('references')]) }}"
                    class="{{ request()->is('data/x') || request()->is('data/x/*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Data
                    X</a>
            @endif

            @if (!request()->has('references'))
                <a href="{{ route('data.index', ['type' => 'y']) }}"
                    class="{{ request()->is('data/y') || request()->is('data/y/*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Data
                    Y</a>
            @else
                <a href="{{ route('data.index', ['type' => 'y', 'reference' => request()->query('references')]) }}"
                    class="{{ request()->is('data/y') || request()->is('data/y/*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Data
                    Y</a>
            @endif

            @if (!request()->has('references'))
                <a href="{{ route('analysis') }}"
                    class="{{ request()->is('analysis') || request()->is('analysis/*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Hasil</a>
            @else
                <a href="{{ route('analysis', request()->query('references')) }}"
                    class="{{ request()->is('analysis') || request()->is('analysis/*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} block px-3 py-2 rounded-md text-base font-medium">Hasil</a>
            @endif

            @if (!auth()->check())
                <a href="{{ route('login') }}"
                    class="text-gray-700 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">Masuk</a>
            @else
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left text-gray-700 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                        Logout
                    </button>
                </form>
            @endif
        </div>
    </div>
</header>
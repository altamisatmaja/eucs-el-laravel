@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }

        .pagination li {
            margin: 0 0.25rem;
        }

        .pagination li a {
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            color: #4b5563;
        }

        .pagination li.active a {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .pagination li.disabled a {
            color: #9ca3af;
            pointer-events: none;
        }
    </style>
    <div class="flex flex-col min-h-screen min-w-full py-5">
        <div class="container mx-auto">
            <div class="py-6 rounded justify-center items-center">
                <div
                    class="flex-grow container mx-auto px-4 py-6 border border-gray-300 rounded justify-center items-center">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Selamat datang, {{ auth()->user()->name ?? 'Tamu' }}
                    </h1>
                    <p class="text-gray-600 mb-6">
                        Aplikasi ini bagian dari penelitian skripsi mahasiswa strata satu (S1) Program Studi Sistem
                        Informasi
                        Universitas Tiga Serangkai yang menggunakan metode EUCS untuk mengetahui tingkat kepuasan user
                        terhadap aplikasi "DANA".
                    </p>

                    <div class="p-6">
                        @auth
                            @if (!request()->has('references'))
                                <div class="text-center mb-2">
                                    <button id="openUploadModal"
                                        class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                            </path>
                                        </svg>
                                        Upload Data
                                    </button>
                                </div>
                            @else
                                <div class="text-center mb-2">
                                    <a href="{{ route('dashboard.clear') }}">
                                        <button
                                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md inline-flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            Clear
                                        </button>
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center mb-2">
                                <a href="{{ route('login') }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md inline-flex items-center">
                                    Masuk
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            @auth
                @if (request()->has('references'))
                    <!-- Show records and tables only if they exist (when reference parameter exists) -->
                    @foreach ($recordIds as $recordId)
                        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 rounded border">
                                    <thead class="bg-blue-200">
                                        <tr>
                                            <th scope="col"
                                                class="font-bold px-2 py-3 text-left text-xs text-gray-500 uppercase tracking-wider">
                                                No
                                            </th>
                                            @if (isset($formattedData))
                                                @php
                                                    $variables = array_keys($formattedData);
                                                    sort($variables);

                                                    $displayVariables = array_map(function ($var) {
                                                        if (preg_match('/^([xy])(\d)(\d)$/', $var, $matches)) {
                                                            $prefix = strtoupper($matches[1]);
                                                            $mainNum = $matches[2];
                                                            $subNum = $matches[3];
                                                            return "{$prefix}{$mainNum}.{$subNum}";
                                                        }
                                                        return $var;
                                                    }, $variables);
                                                @endphp

                                                @foreach ($displayVariables as $header)
                                                    <th scope="col"
                                                        class="font-bold px-2 py-3 text-left text-xs text-gray-500 uppercase tracking-wider">
                                                        {{ $header }}
                                                    </th>
                                                @endforeach
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @if (isset($formattedData))
                                            @php
                                                $firstVar = $variables[0] ?? null;
                                                $respondentCount = $firstVar ? count($formattedData[$firstVar]) : 0;
                                            @endphp

                                            @for ($i = 0; $i < $respondentCount; $i++)
                                                <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                                                    <td class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ ($values->currentPage() - 1) * $values->perPage() + $i + 1 }}
                                                    </td>
                                                    @foreach ($variables as $var)
                                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $formattedData[$var][$i] ?? '' }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endfor
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @if (isset($pagination))
                                <div
                                    class="px-4 py-3 bg-white border border-gray-200 sm:px-6">
                                    {{ $values->withQueryString()->links('vendor.pagination.tailwind') }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            @endauth
        </div>

        <!-- Upload Modal -->
        <div id="uploadModal" class="fixed inset-0 z-50 hidden">
            <div class="flex items-center justify-center min-h-screen">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>

                <!-- Modal content -->
                <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full m-4 relative">
                    <form action="{{ route('data.upload') }}" method="POST" enctype="multipart/form-data" class="p-6">
                        @csrf
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Upload Data</h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Pilih File Excel
                                    (.xls, .xlsx, csv)</label>
                                <input type="file" id="file" name="file" required
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" onclick="closeModal()"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Tutup
                            </button>
                            <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Modal functions
        function openModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Open modal
            const uploadBtn = document.getElementById('openUploadModal');
            if (uploadBtn) {
                uploadBtn.addEventListener('click', openModal);
            }

            // Close modal with ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
        });
    </script>
@endsection

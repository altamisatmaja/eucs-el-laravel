@extends('layouts.app')

@section('content')
    <div class="flex flex-col min-h-screen w-full">
        <div class="flex justify-center">
            <div class="pb-10 pt-3">
                <div class="bg-white rounded-lg overflow-hidden border">
                    <div class="bg-blue-600 text-white px-6 py-4">
                        <h2 class="text-xl font-semibold">Edit Data Responden</h2>
                    </div>

                    <div class="p-6">
                        <form method="POST" action="{{ route('data.updateRespondent', $id) }}">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                @foreach ($variables as $variable)
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
                                        <label for="{{ $variable }}" class="md:col-span-2 text-gray-700 font-medium">
                                            {{ $variable }}
                                        </label>
                                        <div class="md:col-span-3">
                                            <input
                                                id="{{ $variable }}"
                                                type="text"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error($variable) border-red-500 @enderror"
                                                name="{{ $variable }}"
                                                value="{{ old($variable, $data[$variable] ?? '') }}"
                                                required
                                            >
                                            @error($variable)
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex items-center justify-end mt-8 space-x-3">
                                <a href="{{ url()->previous() }}"
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
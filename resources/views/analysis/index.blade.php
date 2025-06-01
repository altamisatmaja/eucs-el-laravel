@extends('layouts.app')

@section('title', 'Hasil')

@section('content')
    <div class="container mx-auto py-5">
        @if (!request()->has('references'))
            <div class="flex flex-col min-h-screen">
                <div class="rounded justify-center items-center">
                    <div class="flex-grow container mx-auto px-4 py-6 border border-gray-300 rounded justify-center items-center max-w-7xl">
                        <h1 class="text-2xl font-bold text-gray-800 mb-6">Selamat datang, {{ $user->name ?? 'Tamu' }}</h1>
                        <p>Harap upload file terlebih dahulu ya!</p>
                    </div>
                </div>
            </div>
        @elseif (request()->has('references') && empty($results))
            <div class="alert alert-warning">
                Tidak ditemukan data dengan reference ID tersebut.
            </div>
        @else
            
            <div class="card mb-4">
                <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 rounded border">
                            <thead class="bg-blue-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Variabel</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Min</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Max</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Mean</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Var</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Standar Deviasi</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nilai Capaian</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kategori</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $combinedData = [];
                                    foreach ($results['X'] as $var => $stats) {
                                        $combinedData[$var] = [
                                            'stats' => $stats,
                                            'achievement' => $results['achievement']['X'][$var] ?? null
                                        ];
                                    }
                                    foreach ($results['Y'] as $var => $stats) {
                                        $combinedData[$var] = [
                                            'stats' => $stats,
                                            'achievement' => $results['achievement']['Y'][$var] ?? null
                                        ];
                                    }
                                    $index = 1; 
                                @endphp

                                @foreach ($combinedData as $variable => $data)
                                    @php
                                        $stats = $data['stats'];
                                        $achievement = $data['achievement'];
                                        $interpretationClass = '';
                                        $interpretationText = '';
                                        
                                        if ($achievement) {
                                            if ($achievement['achievement_percentage'] >= 90) {
                                                $interpretationClass = 'bg-green-100 text-green-800';
                                                $interpretationText = 'Sangat Baik';
                                            } elseif ($achievement['achievement_percentage'] >= 80) {
                                                $interpretationClass = 'bg-blue-100 text-blue-800';
                                                $interpretationText = 'Baik';
                                            } elseif ($achievement['achievement_percentage'] >= 70) {
                                                $interpretationClass = 'bg-yellow-100 text-yellow-800';
                                                $interpretationText = 'Cukup';
                                            } elseif ($achievement['achievement_percentage'] >= 60) {
                                                $interpretationClass = 'bg-yellow-100 text-yellow-800';
                                                $interpretationText = 'Kurang';
                                            } else {
                                                $interpretationClass = 'bg-red-100 text-red-800';
                                                $interpretationText = 'Sangat Kurang';
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">{{ $index }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">{{ strtoupper($variable) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $stats['min'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $stats['max'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ number_format($stats['mean'], 2) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ number_format($stats['variance'], 2) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ number_format($stats['std_dev'], 2) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap font-semibold">
                                            @if($achievement)
                                                {{ number_format($achievement['achievement_percentage'], 2) }}%
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($achievement)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $interpretationClass }}">
                                                    {{ $interpretationText }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @php $index++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="bg-white mb-8">
                <h4 class="font-bold text-lg mb-3">Panduan Kategori</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h5 class="font-semibold mb-2">Nilai Capaian:</h5>
                        <ul class="space-y-2">
                            <li class="flex items-center"><span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span> Sangat Baik (90-100%)</li>
                            <li class="flex items-center"><span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span> Baik (80-90%)</li>
                            <li class="flex items-center"><span class="inline-block w-3 h-3 bg-yellow-500 rounded-full mr-2"></span> Cukup (70-80%)</li>
                            <li class="flex items-center"><span class="inline-block w-3 h-3 bg-red-500 rounded-full mr-2"></span> Kurang (60-70%)</li>
                            <li class="flex items-center"><span class="inline-block w-3 h-3 bg-gray-800 rounded-full mr-2"></span> Sangat Kurang (<60%)</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
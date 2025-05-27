@extends('layouts.app')

@section('content')
    <div class="flex flex-col min-h-screen min-w-full">
        <div class="container mx-auto">
            @if (!request()->has('references') && empty($formattedData))
                <div class="flex flex-col min-h-screen">
                    <div class="px-4 py-6  rounded justify-center items-center">
                        <div
                            class="flex-grow container mx-auto px-4 py-6 border border-gray-300 rounded justify-center items-center max-w-7xl">
                            <h1 class="text-2xl font-bold text-gray-800 mb-6">Selamat datang, {{ $user->name ?? 'Tamu' }}
                            </h1>
                            <p>Harap upload file terlebih dahulu ya!
                            </p>
                        </div>
                    </div>
                </div>
            @elseif (request()->has('references') && empty($formattedData))
                <div class="alert alert-warning">
                    Tidak ditemukan data dengan reference ID tersebut.
                </div>
            @else
                @foreach ($recordIds as $recordId)
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('data.index', ['type' => $type, 'references' => $recordId]) }}"
                                class="btn btn-sm btn-primary"></a>
                        </div>
                    </div>
                    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
                        <div class="overflow-x-auto">
                            <div class="">
                                <table class="min-w-full divide-y divide-gray-200 rounded border">
                                    <thead class="bg-blue-200">
                                        <tr>
                                            @if (isset($formattedData[$recordId]))
                                                @php
                                                    $variables = array_keys($formattedData[$recordId]);
                                                    $variables = array_filter($variables, function ($var) use ($type) {
                                                        return strtoupper(substr($var, 0, 1)) === $type;
                                                    });
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
                                                <th scope="col"
                                                    class="font-bold px-2 py-3 text-left text-xs text-gray-500 uppercase tracking-wider">
                                                    No
                                                </th>
                                                @foreach ($displayVariables as $header)
                                                    <th scope="col"
                                                        class="font-bold px-2 py-3 text-left text-xs text-gray-500 uppercase tracking-wider">
                                                        {{ $header }}
                                                    </th>
                                                @endforeach
                                                <th scope="col"
                                                    class="font-bold px-2 py-3 text-left text-xs text-gray-500 uppercase tracking-wider">
                                                    Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @if (isset($formattedData[$recordId]))
                                            @php
                                                $filteredVariables = array_filter(
                                                    array_keys($formattedData[$recordId]),
                                                    function ($var) use ($type) {
                                                        return strtoupper(substr($var, 0, 1)) === $type;
                                                    },
                                                );
                                                sort($filteredVariables);

                                                $firstVar = $filteredVariables[0] ?? null;
                                                $respondentCount = $firstVar
                                                    ? count($formattedData[$recordId][$firstVar])
                                                    : 0;
                                            @endphp

                                            @for ($i = 0; $i < $respondentCount; $i++)
                                                @php
                                                    $firstRecordValue = App\Models\RecordValue::where(
                                                        'record_id',
                                                        $recordId,
                                                    )
                                                        ->where('variable', $filteredVariables[0])
                                                        ->skip($i)
                                                        ->first();
                                                @endphp

                                                <tr
                                                    class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                                                    <td
                                                        class="px-2 py-1 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $i + 1 }}</td>
                                                    @foreach ($filteredVariables as $var)
                                                        <td class="py-1 whitespace-nowrap text-sm text-gray-500">
                                                            @php
                                                                $recordValue = App\Models\RecordValue::where(
                                                                    'record_id',
                                                                    $recordId,
                                                                )
                                                                    ->where('variable', $var)
                                                                    ->skip($i)
                                                                    ->first();
                                                            @endphp
                                                            {{ $formattedData[$recordId][$var][$i] ?? '' }}
                                                        </td>
                                                    @endforeach
                                                    <td class="py-2 flex flex-wrap gap-2">
                                                        @if ($firstRecordValue)
                                                            <a href="{{ route('data.editRespondent', $firstRecordValue->id) }}"
                                                                class="btn btn-sm btn-primary">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                    fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                            </a>
                                                            <form
                                                                action="{{ route('data.destroyRespondent', $firstRecordValue->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh data responden ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                        fill="none" viewBox="0 0 24 24"
                                                                        stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endfor
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection

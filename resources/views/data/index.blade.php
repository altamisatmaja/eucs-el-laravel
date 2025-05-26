@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Data {{ $type }}</h2>

        @if (!request()->has('references') && empty($formattedData))
            <div class="alert alert-info">
                Harap upload data terlebih dahulu.
                <a href="{{ route('data.upload') }}" class="alert-link">Upload Data</a>
            </div>
        @elseif (request()->has('references') && empty($formattedData))
            <div class="alert alert-warning">
                Tidak ditemukan data dengan reference ID tersebut.
            </div>
        @else
            @foreach ($recordIds as $recordId)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Record ID: {{ $recordId }}</span>
                        <div>
                            <a href="{{ route('data.index', ['type' => $type, 'references' => $recordId]) }}"
                                class="btn btn-sm btn-primary">Detail</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        @if (isset($formattedData[$recordId]))
                                            @php
                                                // Filter variables based on current type
                                                $variables = array_keys($formattedData[$recordId]);
                                                $variables = array_filter($variables, function ($var) use ($type) {
                                                    return strtoupper(substr($var, 0, 1)) === $type;
                                                });
                                                sort($variables);

                                                // Format variable names for headers
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
                                            <th>No</th>
                                            @foreach ($displayVariables as $header)
                                                <th>{{ $header }}</th>
                                            @endforeach
                                            <th>Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
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
                                                // Get the first record value for this respondent to use for edit/delete
                                                $firstRecordValue = App\Models\RecordValue::where('record_id', $recordId)
                                                    ->where('variable', $filteredVariables[0])
                                                    ->skip($i)
                                                    ->first();
                                            @endphp

                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                @foreach ($filteredVariables as $var)
                                                    <td>
                                                        @php
                                                            $recordValue = App\Models\RecordValue::where('record_id', $recordId)
                                                                ->where('variable', $var)
                                                                ->skip($i)
                                                                ->first();
                                                        @endphp
                                                        {{ $formattedData[$recordId][$var][$i] ?? '' }}
                                                    </td>
                                                @endforeach
                                                <td class="flex space-x-2">
                                                    @if ($firstRecordValue)
                                                        <a href="{{ route('data.editRespondent', $firstRecordValue->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit
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
                                                                Hapus
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
@endsection

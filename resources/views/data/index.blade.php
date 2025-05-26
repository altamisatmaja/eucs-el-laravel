@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Data {{ $type }}</h2>

        @foreach ($recordIds as $recordId)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Record ID: {{ $recordId }}</span>
                    <div>
                        <a href="{{ route('data.index', ['type' => $type, 'reference' => $recordId]) }}"
                            class="btn btn-sm btn-primary">Detail</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="thead-light">
                                <tr>
                                    @if (isset($formattedData[$recordId]))
                                        @php
                                            // Filter variables based on current type
                                            $variables = array_keys($formattedData[$recordId]);
                                            $variables = array_filter($variables, function($var) use ($type) {
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
                            
                                        @foreach ($displayVariables as $header)
                                            <th>{{ $header }}</th>
                                        @endforeach
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($formattedData[$recordId]))
                                    @php
                                        $filteredVariables = array_filter(array_keys($formattedData[$recordId]), function($var) use ($type) {
                                            return strtoupper(substr($var, 0, 1)) === $type;
                                        });
                                        sort($filteredVariables);
                                        
                                        $firstVar = $filteredVariables[0] ?? null;
                                        $respondentCount = $firstVar ? count($formattedData[$recordId][$firstVar]) : 0;
                                    @endphp
                            
                                    @for ($i = 0; $i < $respondentCount; $i++)
                                        <tr>
                                            @foreach ($filteredVariables as $var)
                                                <td>{{ $formattedData[$recordId][$var][$i] ?? '' }}</td>
                                            @endforeach
                                        </tr>
                                    @endfor
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
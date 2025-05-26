@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>EUCS Data Analysis</h2>

        @if (!request()->has('references'))
            <div class="alert alert-info">
                Harap upload data terlebih dahulu.
                <a href="{{ route('data.upload') }}" class="alert-link">Upload Data</a>
            </div>
        @elseif (request()->has('references') && empty($results))
            <div class="alert alert-warning">
                Tidak ditemukan data dengan reference ID tersebut.
            </div>
        @else
            @if ($reference)
                <div class="alert alert-info">Showing analysis for record: {{ $reference }}</div>
            @endif

            @foreach (['X', 'Y'] as $type)
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Type {{ $type }} EUCS Statistics</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Variable</th>
                                        <th>N</th>
                                        <th>Sum</th>
                                        <th>Mean</th>
                                        <th>Sum of Squares</th>
                                        <th>Variance</th>
                                        <th>Std Dev</th>
                                        <th>Min</th>
                                        <th>Max</th>
                                        <th>Range</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results[$type] as $variable => $stats)
                                        <tr>
                                            <td>{{ strtoupper($variable) }}</td>
                                            <td>{{ $stats['n'] }}</td>
                                            <td>{{ number_format($stats['sum'], 2) }}</td>
                                            <td>{{ number_format($stats['mean'], 2) }}</td>
                                            <td>{{ number_format($stats['sum_squares'], 2) }}</td>
                                            <td>{{ number_format($stats['variance'], 2) }}</td>
                                            <td>{{ number_format($stats['std_dev'], 2) }}</td>
                                            <td>{{ $stats['min'] }}</td>
                                            <td>{{ $stats['max'] }}</td>
                                            <td>{{ number_format($stats['range'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Achievement Scores Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Type {{ $type }} Achievement Scores</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Variable</th>
                                        <th>Mean Score</th>
                                        <th>Achievement Percentage</th>
                                        <th>Interpretation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results['achievement'][$type] as $variable => $stats)
                                        <tr>
                                            <td>{{ strtoupper($variable) }}</td>
                                            <td>{{ number_format($stats['mean'], 2) }}</td>
                                            <td>{{ number_format($stats['achievement_percentage'], 2) }}%</td>
                                            <td
                                                class="
                                        @if ($stats['achievement_percentage'] >= 80) table-success
                                        @elseif($stats['achievement_percentage'] >= 70) table-info
                                        @elseif($stats['achievement_percentage'] >= 60) table-warning
                                        @else table-danger @endif
                                    ">
                                                {{ $stats['interpretation'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <p><strong>Interpretation Guide:</strong></p>
                            <ul>
                                <li><span class="badge bg-success">Sangat Baik</span> (≥80%)</li>
                                <li><span class="badge bg-info">Baik</span> (70-79%)</li>
                                <li><span class="badge bg-warning">Cukup</span> (60-69%)</li>
                                <li><span class="badge bg-danger">Kurang</span> (50-59%)</li>
                                <li><span class="badge bg-dark">Sangat Kurang</span> (<50%)< /li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="card mb-4">
                <div class="card-header">
                    <h3>X-Y Dimension Comparison (Euclidean Distance)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Variable</th>
                                    <th>Distance</th>
                                    <th>Similarity</th>
                                    <th>Interpretation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results['comparison'] as $variable => $stats)
                                    <tr>
                                        <td>{{ strtoupper($variable) }}</td>
                                        <td>{{ number_format($stats['distance'], 4) }}</td>
                                        <td>{{ number_format($stats['similarity'], 4) }}</td>
                                        <td>{{ $stats['interpretation'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection

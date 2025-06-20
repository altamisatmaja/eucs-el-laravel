@extends('layouts.app')

@section('title', 'Edit Data')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Data</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('data.update', $record->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row">
                            <label for="variable" class="col-md-4 col-form-label text-md-right">Variable</label>
                            <div class="col-md-6">
                                <input id="variable" type="text" class="form-control" value="{{ $record->variable }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="value" class="col-md-4 col-form-label text-md-right">Value</label>
                            <div class="col-md-6">
                                <input id="value" type="text" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ $record->value }}" required>

                                @error('value')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layout')

@section('content')
    <div class="flex-center position-ref full-height">
        <p>These companies match your requirements:</p>

        @forelse ($companies as $company => $condition)
            @include('company-requirements', [
                'company' => $company,
                'condition' => $condition,
            ])
        @empty
            <div>Sorry, no jobs found for these requirements...</div>
        @endforelse
    </div>
@endsection

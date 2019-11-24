@extends('layout')

@section('content')
    <div class="flex-center position-ref full-height">
        <p>These companies match your requirements:</p>

        @forelse ($companies as $company => $requirementSet)
            <div id="company-list">
                <p>
                    {{ $company }} requires

                    @forelse ($requirementSet as $requirement)
                        {{ $requirement }}

                        @if ($loop->index < \count($requirementSet) - 1)
                            and
                        @else
                            .
                        @endif
                    @empty
                        nothing. You are free to apply.
                    @endforelse
                </p>
            </div>
        @empty
            <div>Sorry, no jobs found for these requirements...</div>
        @endforelse
    </div>
@endsection

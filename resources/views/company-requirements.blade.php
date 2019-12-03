<div id="company-list">
    <p>
        {{ $company }} requires

        @forelse ($condition as $requirements)
            @foreach ($requirements as $requirement)
                @if ($loop->index === \count($requirements) - 1)
                    {{ $requirement }}
                @else
                    {{ $requirement }} or
                @endif
            @endforeach

            @if ($loop->index < \count($condition) - 1)
                and
            @else
                .
            @endif
        @empty
            nothing. You are free to apply.
        @endforelse
    </p>
</div>

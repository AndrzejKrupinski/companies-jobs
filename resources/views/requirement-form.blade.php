@extends('layout')

@section('content')
    <div class="flex-center position-ref full-height">
        <div class="content">
            <div class="title m-b-md">
                {{ config('app.name') }}
            </div>
            <div>
                <form action="{{ route('company.show') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="requirements">Please specify your qualifications to find job that suits you:</label>
                        <br>
                        <select id="requirements" name="requirements[]" class="form-control" multiple style="width: 300px;">
                        </select>
                    </div>
                    <button class="btn btn-first" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection

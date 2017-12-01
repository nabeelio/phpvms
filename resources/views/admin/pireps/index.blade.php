@extends('admin.app')

@section('title', 'Pilot Reports')

@section('content')
    @include('admin.pireps.table')

    <div class="row">
        <div class="col-12 text-center">
            {{ $pireps->links('admin.pagination.default') }}
        </div>
    </div>
@endsection


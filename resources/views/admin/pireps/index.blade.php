@extends('admin.app')

@section('title', 'Pilot Reports')
@section('actions')
    <li><a href="{!! route('admin.pireps.index') !!}?search=status:0"><i class="ti-plus"></i>Pending</a></li>
    <li><a href="{!! route('admin.pireps.index') !!}"><i class="ti-plus"></i>View All</a></li>
@endsection
@section('content')
    @include('admin.pireps.table')

    <div class="row">
        <div class="col-12 text-center">
            {{ $pireps->links('admin.pagination.default') }}
        </div>
    </div>
@endsection
@include('admin.pireps.script')

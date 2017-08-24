@extends('admin.app')
@section('title', 'pilot report')
@section('actions')
    <li><a href="#"><i class="ti-plus"></i>Accept</a></li>
    <li><a href="#"><i class="ti-plus"></i>Reject</a></li>
@endsection

@section('content')
    <div class="card">
        @include('admin.pireps.show_fields')
        @include('admin.pireps.field_values')
    </div>
@endsection

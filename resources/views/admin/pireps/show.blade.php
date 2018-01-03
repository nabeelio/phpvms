@extends('admin.app')
@section('title', 'pilot report')
@section('actions')
    <li><a href="#"><i class="ti-plus"></i>Accept</a></li>
    <li><a href="#"><i class="ti-plus"></i>Reject</a></li>
@endsection

@section('content')
    <div class="card border-blue-bottom">
        <div class="content">
            @include('admin.pireps.show_fields')
        </div>
    </div>

    <div class="card border-blue-bottom">
        <div class="content">
            <h4>custom fields</h4>
            @include('admin.pireps.field_values')
        </div>
    </div>

    <div class="card border-blue-bottom">
        <div class="content">
            <h4>comments</h4>
            @include('admin.pireps.comments')
        </div>
    </div>

    <div class="card border-blue-bottom">
        <div class="content">
            @include('admin.pireps.map')
        </div>
    </div>
@endsection
@include('admin.pireps.scripts');

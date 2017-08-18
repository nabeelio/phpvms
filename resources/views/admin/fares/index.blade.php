@extends('admin.app')

@section('title', 'Fares')
@section('actions')
    <li>
        <a href="{!! route('admin.fares.create') !!}">
            <i class="ti-plus"></i>
            Add New
        </a>
    </li>
@endsection

@section('content')
    <div class="card">
        @include('admin.fares.table')
    </div>
@endsection


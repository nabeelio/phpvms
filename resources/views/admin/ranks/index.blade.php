@extends('admin.app')
@section('title', 'Ranks')
@section('actions')
    <li>
        <a href="{!! route('admin.ranks.create') !!}">
            <i class="ti-plus"></i>
            Add New</a>
    </li>
@endsection

@section('content')
    <div class="card">
        @include('admin.ranks.table')
    </div>
@endsection
@include('admin.ranks.scripts')

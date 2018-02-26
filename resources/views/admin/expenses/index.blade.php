@extends('admin.app')

@section('title', 'Expenses')
@section('actions')
    <li>
        <a href="{!! route('admin.expenses.create') !!}">
            <i class="ti-plus"></i>
            Add New</a>
    </li>
@endsection

@section('content')
<div class="card border-blue-bottom">
    <div class="content">
        @if(!filled($expenses))
            <p class="text-center">
                You must add a subfleet before you can add an aircraft!
            </p>
        @else
            @include('admin.expenses.table')
        @endif
    </div>
</div>
@endsection


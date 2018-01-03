@extends('admin.app')
@section('Add Rank')
@section('content')
<div class="card border-blue-bottom">
    <div class="content">
        @include('admin.flash.message')
        {!! Form::open(['route' => 'admin.ranks.store', 'class' => 'add_rank', 'method'=>'POST']) !!}
            @include('admin.ranks.fields')
        {!! Form::close() !!}
    </div>
</div>
@endsection
@include('admin.ranks.scripts')

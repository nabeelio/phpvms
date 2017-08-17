@extends('admin.app')

@section('content')
    <div class="card">
        <div class="header">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px"
               href="{!! route('admin.flights.create') !!}">Add New</a>
            <h4 class="title">Flights</h4>
        </div>
        @include('admin.flights.table')
    </div>
    {{--<div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">

            </div>
        </div>
    </div>--}}
@endsection


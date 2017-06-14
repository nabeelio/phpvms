@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">Aircraft</h1>
        <h1 class="pull-right">
           <a class="btn btn-primary " style="margin-top: -8px;" href="{!! url('/admin/aircraftclasses') !!}">Aircraft Classes</a>
            &nbsp;&nbsp;
           <a class="btn btn-primary " style="margin-top: -8px;" href="{!! route('admin.aircraft.create') !!}">Add New Aircraft</a>
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('admin.aircraft.table')
            </div>
        </div>
    </div>
@endsection


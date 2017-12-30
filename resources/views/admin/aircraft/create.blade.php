@extends('admin.app')
@section('title', 'Add Aircraft')
@section('content')
    <section class="content-header">
        <h1>Aircraft</h1>
    </section>
    <div class="content">
        @include('admin.flash.message')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'admin.aircraft.store']) !!}

                        @include('admin.aircraft.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1>Create Flight</h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'admin.flights.store']) !!}

                        @include('admin.flights.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

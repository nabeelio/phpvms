@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1>Add Airport</h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'admin.airports.store']) !!}

                        @include('admin.airports.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

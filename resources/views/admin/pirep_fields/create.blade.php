@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1>Add PIREP Field</h1>
    </section>
    <div class="content">
        @include('admin.flash.message')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'admin.pirepfields.store']) !!}
                        @include('admin.pirep_fields.fields')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

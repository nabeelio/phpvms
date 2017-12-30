@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1>
            $MODEL_NAME_HUMAN$
        </h1>
    </section>
    <div class="content">
        @include('admin.flash.message')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'admin.pireps.store']) !!}

                        @include('admin.pireps.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

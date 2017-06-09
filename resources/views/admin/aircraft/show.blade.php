@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1>
            Aircraft
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('admin.aircraft.show_fields')
                    <a href="{!! route('admin.aircraft.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection

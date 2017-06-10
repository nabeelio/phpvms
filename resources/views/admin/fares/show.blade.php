@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1>
            Fare
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('admin.fares.show_fields')
                    <a href="{!! route('admin.fares.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection

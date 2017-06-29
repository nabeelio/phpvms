@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1>PIREP</h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('admin.pireps.show_fields')
                    <a href="{!! route('admin.pireps.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection

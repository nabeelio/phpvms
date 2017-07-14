@extends('admin.app')

@section('content')
<section class="content-header"><h1>PIREP</h1></section>
<section class="content">
    <div class="clearfix"></div>
    <div class="row">
        @include('admin.pireps.show_fields')
    </div>
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12">
                    <h3>fields</h3>
                    <div class="box-body">
                        @include('admin.pireps.field_values')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

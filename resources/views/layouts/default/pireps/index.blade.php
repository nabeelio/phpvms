@extends("layouts.${SKIN_NAME}.app")
@section('title', 'pireps')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div style="float:right;">
            <a class="btn btn-primary pull-right btn-lg"
               style="margin-top: -10px;margin-bottom: 5px"
               href="{!! route('frontend.pireps.create') !!}">File New PIREP</a>
        </div>
        <h2 class="description">pilot reports</h2>
        @include('flash::message')
        @include("layouts.${SKIN_NAME}.pireps.table")
    </div>
</div>
<div class="row">
    <div class="col-12 text-center">
        {{ $pireps->links("layouts.${SKIN_NAME}.pagination.default") }}
    </div>
</div>
@endsection


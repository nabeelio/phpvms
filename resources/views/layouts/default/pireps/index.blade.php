@extends('layouts.default.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div style="float:right;">
            <a class="btn btn-primary pull-right"
               style="margin-top: -10px;margin-bottom: 5px"
               href="{!! route('frontend.pireps.create') !!}">File New PIREP</a>
        </div>
        <h2 class="description">pilot reports</h2>
        @include('flash::message')
        @include('layouts.default.pireps.table')
    </div>
</div>
@endsection


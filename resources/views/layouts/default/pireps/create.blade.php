@extends('layouts.default.app')
@section('title', 'file pirep')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="description">new pilot report</h2>
            @include('flash::message')
            {!! Form::open(['route' => 'frontend.pireps.store']) !!}

            @include('layouts.default.pireps.fields')

            {!! Form::close() !!}
        </div>
    </div>
@endsection
@section('scripts')
<script>
$(document).ready(function() {

});
</script>
@endsection

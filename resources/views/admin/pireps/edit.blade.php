@extends('admin.app')

@section('title', 'Edit ' . $pirep->getFlightId() )
@section('content')
<div class="content">
   @include('adminlte-templates::common.errors')
   <div class="card border-blue-bottom">
       <div class="content">
           {!! Form::model($pirep, ['route' => ['admin.pireps.update', $pirep->id], 'method' => 'patch']) !!}
                @include('admin.pireps.fields')
           {!! Form::close() !!}
       </div>
   </div>

    <div class="card border-blue-bottom">
        <div class="content">
            @include('admin.pireps.field_values')
        </div>
    </div>
</div>
@endsection
@include('admin.pireps.script')

@extends('admin.app')
@section('title', "Edit \"$rank->name\"")
@section('content')
<div class="card border-blue-bottom">
   <div class="content">
       @include('adminlte-templates::common.errors')
       {!! Form::model($rank, ['route' => ['admin.ranks.update', $rank->id], 'method' => 'patch']) !!}
            @include('admin.ranks.fields')
       {!! Form::close() !!}
   </div>
</div>

<div class="card border-blue-bottom">
    <div class="header">
       <h3>subfleets</h3>
    </div>
    <div class="content">
       <div class="row">
           @include('admin.ranks.subfleets')
       </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function() {
    $(".select2_dropdown").select2();

    $(document).on('submit', 'form.pjax_form', function(event) {
        event.preventDefault();
        $.pjax.submit(event, '#rank_subfleet_wrapper', {push: false});
    });

    $(document).on('pjax:complete', function() {
        $(".select2_dropdown").select2();
    });
});
</script>
@endsection

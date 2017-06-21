@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1>{!! $rank->name !!}</h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($ranking, ['route' => ['admin.ranks.update', $rank->id], 'method' => 'patch']) !!}

                        @include('admin.ranks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection

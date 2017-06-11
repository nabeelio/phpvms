@extends('admin.app')

@section('content')
    <section class="content-header">
        <h1>
            Editing {{ $airport->name }}
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($airport, ['route' => ['admin.airports.update', $airport->id], 'method' => 'patch']) !!}

                        @include('admin.airports.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection

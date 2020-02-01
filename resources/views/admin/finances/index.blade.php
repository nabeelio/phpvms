@extends('admin.app')

@section('title', 'Financial Reports')
@section('actions')
  <li><a href="{{ route('admin.finances.index') }}"><i class="ti-menu-alt"></i>Overview</a></li>
@endsection
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      <div style="float:right;">
        {{ Form::select(
                'month_select',
                $months_list,
                $current_month,
                ['id' => 'month_select']
            ) }}
      </div>

      @include('admin.finances.table')

    </div>
  </div>
@endsection
@include('admin.finances.scripts')

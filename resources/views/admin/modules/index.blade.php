@extends('admin.app')
@section('title', "Modules Manager")
@section('actions')
  <li>
    <a href="{{ route('admin.modules.create') }}">
      <i class="ti-plus"></i>
      Add New</a>
  </li>
@endsection
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      <div class="row">
        <div class="col-lg-12">
          <table class="table table-bordered table-primary">
            <thead>
              <th>Module</th>
              <th>Status</th>
              <th>Actions</th>
            </thead>
            <tbody>
            @forelse($modules as $module)
              <tr>
                <td>
                  {{$module->name}}
                  @if($module->is_new == 1)
                    <b class="text-danger">(Not Installed)</b>
                  @endif
                </td>
                <td>
                  @if($module->enabled == 1)
                    Enabled
                  @else
                    Disabled
                  @endif
                </td>
                <td>
                  @if($module->is_new == 1)
                    {{Form::open(['route' => ['admin.modules.update', $module->id]])}}
                      {{Form::hidden('enabled', 0)}}
                      {{ Form::button('Activate Module', ['type' => 'submit', 'class' => 'btn btn-success']) }}
                    {{Form::close()}}
                  @else
                    <a class="btn btn-primary" href="{{ route('admin.modules.edit', [$module->id]) }}">Edit Module</a>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center">
                  No Modules Installed Yet!
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

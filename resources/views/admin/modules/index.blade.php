@extends('admin.app')
@section('title', 'modules')
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
          <h5>Installed Modules</h5>
          <hr>
          <table class="table table-bordered table-primary">
            <thead>
              <th>Module</th>
              <th>Status</th>
              <th>Actions</th>
            </thead>
            <tbody>
            @forelse($modules as $module)
              <tr>
                <td>{{$module->name}}</td>
                <td>
                  @if($module->enabled == 1)
                    Enabled
                  @else
                    Disabled
                  @endif
                </td>
                <td>
                  <a class="btn btn-primary" href="{{ route('admin.modules.edit', $module->id) }}">Edit Module</a>
                  <a class="btn btn-danger" href="/admin/{{ strtolower($module->name) }}">View Admin Module</a>
                  <a class="btn btn-success" target="_blank" href="/{{ strtolower($module->name) }}">View Frontend Module</a>
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

          @if($new_modules)
            <h5>Not Installed Modules</h5>
            <hr>
            <table class="table table-bordered table-primary">
              <thead>
              <th>Module</th>
              <th>Status</th>
              <th>Actions</th>
              </thead>
              <tbody>
              @foreach($new_modules as $module)
                <tr>
                  <td>{{ $module }}</td>
                  <td>Disabled</td>
                  <td>
                    {{Form::open(['route' => ['admin.modules.enable']])}}
                    {{ Form::hidden('name', $module) }}
                    {{ Form::button('Activate Module', ['type' => 'submit', 'class' => 'btn btn-success']) }}
                    {{Form::close()}}
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

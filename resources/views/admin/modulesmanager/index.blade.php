@extends('admin.app')
@section('title', "Modules Manager")
@section('actions')
  <li>
    <a href="{{ route('admin.modulesmanager.add') }}">
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
                <td>{{$module->name}}</td>
                <td>
                  @if($module->enabled == 1)
                    Enabled
                  @else
                    Disabled
                  @endif
                </td>
                <td>
                  <a class="btn btn-primary" href="{{ route('admin.modulesmanager.edit', [$module->id]) }}">Edit Module</a>
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

@extends('modulesmanager::layouts.admin')

@section('title', 'modulesmanager')
@section('actions')
  <li>
    <a href="{{ url('/modulesmanager/admin/create') }}">
      <i class="ti-plus"></i>
      Add New</a>
  </li>
@endsection
@section('content')
  <div class="card border-blue-bottom" style="padding: 15px">
    <div class="header">
      <h2 class="title">Modules Manager</h2>
    </div>
    <hr>
    <div class="content">
      <div class="row">
        <div class="col-12">
          @if (session('success'))
            <div class="alert alert-success">
              {{ session('success') }}
            </div>
          @elseif(session('error'))
            <div class="alert alert-danger">
              {{ session('error') }}
            </div>
          @endif
        </div>
        <div class="col-lg-6">
          <h5>Please note that : </h5>
          <ul>
            <li>Module Folder must be on top level of the zip.</li>
            <li>Name of the zip must be exactly equal to the name of the module folder inside.</li>
          </ul>


          <h5>Module Zip Structure : </h5>
          <ul>
            <li>
              ModuleName.zip
            </li>
            <ul>
              <li>ModuleName Folder</li>
              <ul>
                <li>Config</li>
                <li>Console</li>
                <li>...</li>
              </ul>
            </ul>
          </ul>
          <hr>
          <form method="post" action="{{route('admin.modulesmanager.addModule')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
              <label>Module File : </label>
              <input type="file" name="module_file" class="form-control" />
            </div>
            <div class="form-group">
              <label>Enabled? : </label>
              <input type="checkbox" name="enabled" checked />
            </div>
            <button class="btn btn-success" type="submit">Add Module</button>
          </form>
          <hr>
        </div>
        <div class="col-lg-6" style="border-left: 1px solid rgba(0, 0 ,0, 0.2)">
          <h5>Installed Modules : </h5>
          <table class="table table-bordered table-dark">
            <thead>
              <th>Module</th>
              <th>Status</th>
              <th>Actions</th>
            </thead>
            <tbody>
            @forelse($modules as $module)
              <tr>
                <td>{{$module->module_name}}</td>
                <td>
                  <div class="row">
                    <div class="col-sm-2">
                      <input type="checkbox" class="form-control" @if($module->enabled == 1) checked @endif />
                    </div>
                    <div class="col-sm-2">
                      <form method="post" action="{{route('admin.modulesmanager.editModule')}}" id="edit-{{$module->id}}">
                        <input type="hidden" name="module_name" value="{{$module->module_name}}" />
                        <input type="hidden" name="enabled" value="{{$module->enabled}}" />
                        <a href="#" title="Edit Module" onclick="editModule({{$module->id}})" >
                          <i class="fa fa-edit"></i>
                        </a>
                      </form>
                    </div>
                  </div>
                </td>

                <td>
                    <form method="post" action="{{route('admin.modulesmanager.deleteModule')}}" id="delete-{{$module->id}}">
                      <input type="hidden" name="module_name" value="{{$module->module_name}}" />
                      <a href="#" title="Delete Module" onclick="deleteModule({{$module->id}})" class="btn btn-sm btn-danger">
                        <i class="fa fa-times"></i>
                      </a>
                    </form>
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
@section('scripts')
  <script>
    function deleteModule(moduleId) {
      if(confirm('Are you sure, you want to delete this module?'))
      {
        document.getElementById('delete-' + moduleId).submit();
      }
    }
    function editModule(moduleId) {
      if(confirm('Are you sure, you want to change module status?'))
      {
        document.getElementById('edit-' + moduleId).submit();
      }
    }
  </script>
@endsection

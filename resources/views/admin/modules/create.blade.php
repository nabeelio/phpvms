@extends('admin.app')
@section('title', "Add Module")
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
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
      <form method="post" action="{{route('admin.modules.store')}}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
          <label>Module File : </label>
          <input type="file" name="module_file" class="form-control" />
        </div>
        <button class="btn btn-success" type="submit">Add Module</button>
      </form>
      <hr>
    </div>
</div>
@endsection

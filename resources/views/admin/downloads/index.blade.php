@extends('admin.app')
@section('title', "Downloads Manager")
@section('actions')
  <li>
    <a href="{{ route('admin.downloads.create') }}">
      <i class="ti-plus"></i>
      Add New</a>
  </li>
@endsection
@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      <div class="row">
        <div class="col-lg-12">
          <h5>Downloads</h5>
          <hr>
          <table class="table table-bordered table-primary">
            <thead>
              <th>File</th>
              <th>Description</th>
              <th>Path</th>
              <th>Actions</th>
            </thead>
            <tbody>
            @forelse($downloads as $download)
              <tr>
                <td>{{$download->name}}</td>
                <td>{{$download->description}}</td>
                <td>{{$download->path}}</td>
                <td>
                  <a class="btn btn-primary" href="{{ route('admin.downloads.edit', $download->id) }}">Edit Download</a>
                  {{ Form::open(['route' => ['admin.downloads.destroy', $download->id], 'method' => 'delete', 'id' => 'delete-download']) }}
                  {{ Form::close() }}
                  <a class="btn btn-danger" onclick="destroy()">Delete Download</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center">
                  No Downloads Added Yet!
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script>
    function destroy() {
      if (confirm('Are you sure, you want to delete this Download?')) {
        document.getElementById('delete-download').submit();
      }
    }
  </script>
@endsection

{{--

Pass in:
    $model    - The model instance this belongs to, e.g: ['model' => $airport]
    $redirect - Where to go to

--}}
<div id="airport-files-wrapper" class="col-12">
  <div class="header">
    <h3>files</h3>
    @component('admin.components.info')
      Add a download link or upload a file to make available
    @endcomponent
  </div>

  @if(count($model->files) === 0)
    @include('admin.common.none_added', ['type' => 'files'])
  @endif

  {{-- Show all the files here --}}
  <table class="table table-hover table-responsive">
    @if(count($model->files))
      <thead>
      <tr>
        <td>Name</td>
        <td>Direct Link</td>
        <td>Downloads</td>
        <td class="text-right"></td>
      </tr>
      </thead>
    @endif
    <tbody>
    @foreach($model->files as $file)
      <tr>
        <td>{{ $file->name }}</td>
        <td><a href="{{ $file->url }}" target="_blank">Link to file</a></td>
        <td>{{$file->download_count}}</td>
        <td class="text-right">
          {{ Form::open(['route' => ['admin.files.delete', $file->id], 'method' => 'delete']) }}
          {{ Form::hidden('id', $file->id) }}
          {{ Form::button('<i class="fa fa-times"></i>', [
                'type' => 'submit',
                'class' => 'btn btn-sm btn-danger btn-icon',
                'onclick' => "return confirm('Are you sure?')"])
          }}
          {{ Form::close() }}
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <hr>
  <div class="row">
    <div class="col-sm-12">
      <div class="text-right">
        {{ Form::open([
            'url' => route('admin.files.store'),
            'method' => 'POST',
            'class' => 'form-inline',
            'files' => true
           ])
        }}

        {{ Form::token() }}

        <span class="required">*</span>
        {{ Form::text('file_name', null, ['class' => 'form-control', 'placeholder' => 'Name']) }}
        {{ Form::text('file_description', null, ['class' => 'form-control', 'placeholder' => 'Description']) }}
        {{ Form::text('url', null, ['class' => 'form-control', 'placeholder' => 'URL']) }}
        {{ Form::file('file', ['class' => 'form-control']) }}

        {{-- Fields for the model --}}
        {{ Form::hidden('ref_model', get_class($model)) }}
        {{ Form::hidden('ref_model_id', $model->id) }}

        {{ Form::submit('Save', [
            'id'    => 'save_file_upload',
            'class' => 'btn btn-success'
           ])
        }}
        <div class="text-danger" style="padding-top: 10px;">
          <span>{{ $errors->first('filename') }}</span>
          <span>{{ $errors->first('url') }}</span>
          <span>{{ $errors->first('file') }}</span>
        </div>

        {{ Form::close() }}
      </div>
    </div>
  </div>

</div>

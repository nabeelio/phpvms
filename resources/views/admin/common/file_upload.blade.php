{{--

Pass in:
    $model    - The model instance this belongs to, e.g: ['model' => $airport]
    $redirect - Where to go to

--}}
<div id="airport-files-wrapper">
    <div class="header">
        <h3>files</h3>
    </div>

    {{-- Show all the files here --}}
    <table class="table table-hover table-responsive">
        @if(count($model->files))
        <thead>
            <tr>
                <td>Name</td>
                <td>Current File</td>
                <td class="text-right"></td>
            </tr>
        </thead>
        @endif
        <tbody>
        @foreach($model->files as $file)
            <tr>
                <td>{{ $file->name }}</td>
                <td><a href="{{ $file->url }}" target="_blank">Link to file</a></td>
                <td class="text-right">
                    {{ Form::open(['route' => ['admin.files.delete', $file->id], 'method' => 'delete']) }}
                    {{ Form::hidden('id', $file->id) }}
                    {{ Form::hidden('redirect', $redirect) }}
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
        <div class="col-12">
            <div class="text-right">
                {{ Form::open([
                    'url' => route('admin.files.store'),
                    'method' => 'POST',
                    'class' => 'form-inline',
                    'files' => true
                   ])
                }}

                {{-- Fields for the model --}}
                {{ Form::hidden('ref_model', get_class($model)) }}
                {{ Form::hidden('ref_model_id', $model->id) }}
                {{ Form::hidden('redirect', $redirect) }}

                {{ Form::label('name', 'Name:') }}&nbsp;<span class="required">*</span>
                {{ Form::text('name', null, ['class' => 'form-control']) }}

                {{ Form::file('file', ['class' => 'form-control']) }}

                {{ Form::submit('Upload', ['class' => 'btn btn-success']) }}
                <p class="text-danger">{{ $errors->first('name') }}</p>
                <p class="text-danger">{{ $errors->first('file') }}</p>

                {{ Form::close() }}
            </div>
        </div>
    </div>

</div>

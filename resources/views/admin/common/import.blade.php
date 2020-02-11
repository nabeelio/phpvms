<div class="card border-blue-bottom">
  <div class="content">
    <div class="row">

      {{ Form::open(['method' => 'post', 'route' => $route, 'files' => true]) }}
      <div class="form-group col-12">
        {{ Form::label('csv_file', 'Choose a CSV file to import') }}
        {{ Form::file('csv_file', ['accept' => '.csv']) }}
        <p class="text-danger">{{ $errors->first('csv_file') }}</p>
        <div class="checkbox">
          <label class="checkbox-inline">
            {{ Form::label('delete', 'Delete existing data:') }}
            {{ Form::hidden('delete', 0, false) }}
            {{ Form::checkbox('delete') }}
          </label>
        </div>
      </div>

      <div class="form-group col-md-12">
        <div class="text-right">
          {{ Form::button('Start Import', ['type' => 'submit', 'class' => 'btn btn-success']) }}
        </div>
      </div>

      {{ Form::close() }}

      <div class="form-group col-md-12">
        @if($logs['success'])
          <h4>Logs</h4>
          @foreach($logs['success'] as $line)
            <p>{{ $line }}</p>
          @endforeach
        @endif

        @if($logs['errors'])
          <h4>Errors</h4>
          @foreach($logs['errors'] as $line)
            <p>{{ $line }}</p>
          @endforeach
        @endif
      </div>
    </div>
  </div>
</div>

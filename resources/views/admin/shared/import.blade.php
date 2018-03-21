<div class="card border-blue-bottom">
    <div class="content">
        {{ Form::open(['method' => 'post', 'route' => $route, 'files' => true]) }}

        <div class="row">
            <div class="form-group col-12">
                {{ Form::label('csv_file', 'Chose a CSV file to import') }}
                {{ Form::file('csv_file', ['accept' => '.csv']) }}
            </div>

            <div class="form-group col-md-12">
                <div class="text-right">
                    {{ Form::button('Start Import', ['type' => 'submit', 'class' => 'btn btn-success']) }}
                </div>
            </div>

        {{ Form::close() }}

            <div class="form-group col-md-12">
                <h4>Logs</h4>
                @foreach($logs['success'] as $line)
                    <p>{{ $line }}</p>
                @endforeach

                <h4>Errors</h4>
                @foreach($logs['failed'] as $line)
                    <p>{{ $line }}</p>
                @endforeach
            </div>
        </div>
    </div>
</div>

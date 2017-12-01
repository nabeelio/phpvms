<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::open(['route' => 'admin.flights.index', 'method' => 'GET', 'class'=>'form-inline pull-right']) !!}
                {!! Form::label('search', 'search:') !!}
                {!! Form::text('search', null, ['class' => 'form-control']) !!}
                {!! Form::submit('find', ['class' => 'btn btn-primary']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

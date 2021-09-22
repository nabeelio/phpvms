<div class="content">
  <div class="row">
    <div class="col-sm-12">
      <div class="form-group">
        {{ Form::open(['route' => 'admin.flights.index', 'method' => 'GET', 'class'=>'form-inline pull-right']) }}
        <div class="row">
          <div class="form-group col-sm-2">
            {{ Form::label('airlines', 'Airline:') }}
            {{ Form::select('airline_id', $airlines, null , ['class' => 'form-control select2']) }}
          </div>
          <div class="form-group input-group-sm col-sm-2">
            {{ Form::label('flight_number', 'Flight Number:') }}
            {{ Form::text('flight_number', null, ['class' => 'form-control']) }}
          </div>
          <div class="form-group col-sm-4">
            {{ Form::label('dpt_airport_id', 'Departure:') }}
            {{ Form::select('dpt_airport_id', $airports, null , ['class' => 'form-control select2']) }}
          </div>
          <div class="form-group col-sm-4">
            {{ Form::label('arr_airport_id', 'Arrival:') }}
            {{ Form::select('arr_airport_id', $airports, null , ['class' => 'form-control select2']) }}
          </div>
        </div>
        <div class="row text-right">
          <div class="col-sm-12">
            {{ Form::submit('find', ['class' => 'btn btn-primary']) }}
            &nbsp;&nbsp;
            <a href="{{ route('admin.flights.index') }}">clear</a>
          </div>
        </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

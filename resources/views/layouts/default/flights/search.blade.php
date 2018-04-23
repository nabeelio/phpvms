<h3 class="description">search</h3>
<div class="card pull-right">
    <div class="card-block" style="min-height: 0px">
        <div class="form-group">
            {{ Form::open([
                    'route' => 'frontend.flights.search',
                    'method' => 'GET',
                    'class'=>'form-inline'
            ]) }}
            <div>
                <p>Flight Number</p>
                {{ Form::text('flight_number', null, ['class' => 'form-control']) }}
            </div>

            <div style="margin-top: 10px;">
                <p>Departure Airport</p>
                {{ Form::select('dep_icao', $airports, null , ['class' => 'form-control']) }}
            </div>

            <div style="margin-top: 10px;">
                <p>Arrival Airport</p>
                {{ Form::select('arr_icao', $airports, null , ['class' => 'form-control']) }}
            </div>

            <div class="clear" style="margin-top: 10px;">
                {{ Form::submit('find', ['class' => 'btn btn-primary']) }}&nbsp;
                <a href="{{ route('frontend.flights.index') }}">clear</a>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<h2 class="description">search</h2>
<div class="card">
    <div class="card-block" style="min-height: 0px">
        <div class="form-group text-right">
            {!! Form::open(['route' => 'frontend.flights.search', 'method' => 'GET', 'class'=>'form-inline pull-right']) !!}

            <div>
            <p>Flight Number</p>
            {!! Form::text('flight_number', null, ['class' => 'form-control']) !!}
            </div>

            <div>
            <p>Departure Airport</p>
            {!! Form::select('dep_icao', $airports, null , ['class' => 'form-control']) !!}
            </div>

            <div class="">
                <p>Arrival Airport</p>
                {!! Form::select('arr_icao', $airports, null , ['class' => 'form-control']) !!}
            </div>

            <br />
            <div class="">
                {!! Form::submit('find', ['class' => 'btn btn-primary']) !!}&nbsp;
                <a href="{!! route('frontend.flights.index') !!}">clear</a>
            </div>
            <br />
            {!! Form::close() !!}
        </div>
    </div>
</div>

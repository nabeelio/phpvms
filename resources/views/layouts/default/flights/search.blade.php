<h3 class="description">@lang('flights.search')</h3>
<div class="card pull-right">
    <div class="card-block" style="min-height: 0px">
        <div class="form-group">
            {{ Form::open([
                    'route' => 'frontend.flights.search',
                    'method' => 'GET',
                    'class'=>'form-inline'
            ]) }}
            <div>
                <p>@lang('flights.flightnumber')</p>
                {{ Form::text('flight_number', null, ['class' => 'form-control']) }}
            </div>

            <div style="margin-top: 10px;">
                <p>@lang('airports.departure')</p>
                {{ Form::select('dep_icao', $airports, null , ['class' => 'form-control']) }}
            </div>

            <div style="margin-top: 10px;">
                <p>@lang('airports.arrival')</p>
                {{ Form::select('arr_icao', $airports, null , ['class' => 'form-control']) }}
            </div>

            <div class="clear" style="margin-top: 10px;">
                {{ Form::submit(__('common.find'), ['class' => 'btn btn-primary']) }}&nbsp;
                <a href="{{ route('frontend.flights.index') }}">@lang('common.reset')</a>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

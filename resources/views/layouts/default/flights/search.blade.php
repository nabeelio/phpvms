<h3 class="description">@lang('frontend.flights.search')</h3>
<div class="card pull-right">
    <div class="card-block" style="min-height: 0px">
        <div class="form-group">
            {{ Form::open([
                    'route' => 'frontend.flights.search',
                    'method' => 'GET',
                    'class'=>'form-inline'
            ]) }}
            <div>
                <p>@lang('common.flightnumber')</p>
                {{ Form::text('flight_number', null, ['class' => 'form-control']) }}
            </div>

            <div style="margin-top: 10px;">
                <p>@lang('common.departureairport')</p>
                {{ Form::select('dep_icao', $airports, null , ['class' => 'form-control']) }}
            </div>

            <div style="margin-top: 10px;">
                <p>@lang('common.arrivalairport')</p>
                {{ Form::select('arr_icao', $airports, null , ['class' => 'form-control']) }}
            </div>

            <div class="clear" style="margin-top: 10px;">
                {{ Form::submit(trans('frontend.global.find'), ['class' => 'btn btn-primary']) }}&nbsp;
                <a href="{{ route('frontend.flights.index') }}">@lang('common.reset')</a>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

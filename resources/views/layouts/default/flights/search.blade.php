<h3 class="description">{{ __('Search') }}</h3>
<div class="card pull-right">
    <div class="card-block" style="min-height: 0px">
        <div class="form-group">
            {{ Form::open([
                    'route' => 'frontend.flights.search',
                    'method' => 'GET',
                    'class'=>'form-inline'
            ]) }}
            <div>
                <p>{{ __('Flight Number') }}</p>
                {{ Form::text('flight_number', null, ['class' => 'form-control']) }}
            </div>

            <div style="margin-top: 10px;">
                <p>{{ __('Departure Airport') }}</p>
                {{ Form::select('dep_icao', $airports, null , ['class' => 'form-control']) }}
            </div>

            <div style="margin-top: 10px;">
                <p>{{ __('Arrival Airport') }}</p>
                {{ Form::select('arr_icao', $airports, null , ['class' => 'form-control']) }}
            </div>

            <div class="clear" style="margin-top: 10px;">
                {{ Form::submit(__('Find'), ['class' => 'btn btn-primary']) }}&nbsp;
                <a href="{{ route('frontend.flights.index') }}">{{ __('Reset') }}</a>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

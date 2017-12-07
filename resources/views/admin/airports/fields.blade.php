<div class="row">
<div class="col-lg-12">
    <!-- Icao Field -->
    <div class="row">
        <div class="form-group col-sm-6">
            {!! Form::label('icao', 'ICAO:') !!}
            <a href="#" class="airport_data_lookup">Lookup</a>
            {!! Form::text('icao', null, [
                'id' => 'airport_icao', 'class' => 'form-control',
                'rv-value' => 'airport.icao'
                ]) !!}
        </div>

        <div class="form-group col-sm-6">
            {!! Form::label('iata', 'IATA:') !!}
            {!! Form::text('iata', null, ['class' => 'form-control', 'rv-value' => 'airport.iata']) !!}
        </div>
    </div>

    <div class="row">
        <div class="form-group col-sm-6">
            {!! Form::label('name', 'Name:') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'rv-value' => 'airport.name']) !!}
        </div>

        <div class="form-group col-sm-6">
            {!! Form::label('location', 'Location:') !!}
            {!! Form::text('location', null, ['class' => 'form-control', 'rv-value' => 'airport.city']) !!}
        </div>
    </div>

    <div class="row">
        <div class="form-group col-sm-6">
            {!! Form::label('country', 'Country:') !!}
            {!! Form::text('country', null, ['class' => 'form-control', 'rv-value' => 'airport.country']) !!}
        </div>

        <div class="form-group col-sm-6">
            {!! Form::label('tz', 'Timezone:') !!}
            {!! Form::select('tz', $timezones, null, ['class' => 'select2']); !!}
        </div>
    </div>

    <div class="row">
        <div class="form-group col-sm-6">
            {!! Form::label('lat', 'Latitude:') !!}
            {!! Form::number('lat', null, ['class' => 'form-control', 'step' => '0.000001', 'rv-value' => 'airport.lat']) !!}
        </div>

        <div class="form-group col-sm-6">
            {!! Form::label('lon', 'Longitude:') !!}
            {!! Form::number('lon', null, ['class' => 'form-control', 'step' => '0.000001', 'rv-value' => 'airport.lon']) !!}
        </div>
    </div>

    <div class="row">
        <!-- Submit Field -->
        <div class="form-group col-sm-12">
            <div class="pull-right">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{!! route('admin.airports.index') !!}" class="btn btn-default">Cancel</a>
            </div>
        </div>
    </div>
</div>
</div>

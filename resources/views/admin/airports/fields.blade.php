<div class="row">
<div class="col-lg-12">
    <!-- Icao Field -->
    <div class="row">
        <div class="form-group col-sm-6">
            {!! Form::label('icao', 'ICAO:') !!}&nbsp;<span class="required">*</span>
            <a href="#" class="airport_data_lookup">Lookup</a>
            {!! Form::text('icao', null, [
                'id' => 'airport_icao', 'class' => 'form-control'
                ]) !!}
        </div>

        <div class="form-group col-sm-6">
            {!! Form::label('name', 'Name:') !!}&nbsp;<span class="required">*</span>
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="row">
        <div class="form-group col-sm-6">
            {!! Form::label('lat', 'Latitude:') !!}&nbsp;<span class="required">*</span>
            {!! Form::number('lat', null, ['class' => 'form-control', 'step' => '0.000001', 'rv-value' => 'airport.lat']) !!}
        </div>

        <div class="form-group col-sm-6">
            {!! Form::label('lon', 'Longitude:') !!}&nbsp;<span class="required">*</span>
            {!! Form::number('lon', null, ['class' => 'form-control', 'step' => '0.000001', 'rv-value' => 'airport.lon']) !!}
        </div>
    </div>

    <div class="row">
        <div class="form-group col-sm-6">
            {!! Form::label('iata', 'IATA:') !!}
            {!! Form::text('iata', null, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group col-sm-6">
            {!! Form::label('location', 'Location:') !!}
            {!! Form::text('location', null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="row">
        <div class="form-group col-sm-6">
            {!! Form::label('country', 'Country:') !!}
            {!! Form::text('country', null, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group col-sm-6">
            {!! Form::label('tz', 'Timezone:') !!}
            {!! Form::select('tz', $timezones, null, ['class' => 'select2']); !!}
        </div>
    </div>

    <div class="row">
        <div class="form-group col-sm-4">
            {!! Form::label('hub', 'Hub:') !!}
            {!! Form::hidden('hub', 0)  !!}
            {!! Form::checkbox('hub', null) !!}
        </div>
        <!-- Submit Field -->
        <div class="form-group col-sm-8">
            <div class="text-right">
                {!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
                <a href="{!! route('admin.airports.index') !!}" class="btn btn-default">Cancel</a>
            </div>
        </div>
    </div>
</div>
</div>

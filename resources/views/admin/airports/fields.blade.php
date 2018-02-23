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
            <p class="text-danger">{{ $errors->first('icao') }}</p>
        </div>

        <div class="form-group col-sm-6">
            {!! Form::label('iata', 'IATA:') !!}
            {!! Form::text('iata', null, ['class' => 'form-control']) !!}
            <p class="text-danger">{{ $errors->first('iata') }}</p>
        </div>

    </div>

    <div class="row">
        <div class="form-group col-sm-4">
            {!! Form::label('name', 'Name:') !!}&nbsp;<span class="required">*</span>
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
            <p class="text-danger">{{ $errors->first('name') }}</p>
        </div>
        <div class="form-group col-sm-4">
            {!! Form::label('lat', 'Latitude:') !!}&nbsp;<span class="required">*</span>
            {!! Form::number('lat', null, ['class' => 'form-control', 'step' => '0.000001', 'rv-value' => 'airport.lat']) !!}
            <p class="text-danger">{{ $errors->first('lat') }}</p>
        </div>

        <div class="form-group col-sm-4">
            {!! Form::label('lon', 'Longitude:') !!}&nbsp;<span class="required">*</span>
            {!! Form::number('lon', null, ['class' => 'form-control', 'step' => '0.000001', 'rv-value' => 'airport.lon']) !!}
            <p class="text-danger">{{ $errors->first('lon') }}</p>
        </div>
    </div>

    <div class="row">

        <div class="form-group col-sm-4">
            {!! Form::label('country', 'Country:') !!}
            {!! Form::text('country', null, ['class' => 'form-control']) !!}
            <p class="text-danger">{{ $errors->first('country') }}</p>
        </div>

        <div class="form-group col-sm-4">
            {!! Form::label('location', 'Location:') !!}
            {!! Form::text('location', null, ['class' => 'form-control']) !!}
            <p class="text-danger">{{ $errors->first('location') }}</p>
        </div>

        <div class="form-group col-sm-4">
            {!! Form::label('timezone', 'Timezone:') !!}
            {!! Form::select('timezone', $timezones, null, ['id' => 'timezone', 'class' => 'select2']); !!}
            <p class="text-danger">{{ $errors->first('timezone') }}</p>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-sm-6">
            {!! Form::label('ground_handling_cost', 'Ground Handling Cost:') !!}

            @component('admin.components.info')
                This is the base rate per-flight. A multiplier for this rate can be
                set in the subfleet, so you can modulate those costs from there.
            @endcomponent

            {!! Form::number('ground_handling_cost', null, ['class' => 'form-control']) !!}
            <p class="text-danger">{{ $errors->first('ground_handling_cost') }}</p>
        </div>

        <div class="form-group col-md-6">

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

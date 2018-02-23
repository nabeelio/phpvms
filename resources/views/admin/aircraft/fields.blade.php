<div class="row">
    <div class="form-group col-sm-6">
        {!! Form::label('subfleet_id', 'Subfleet:') !!}
        {!! Form::select('subfleet_id', $subfleets, null, ['class' => 'form-control select2', 'placeholder' => 'Select Subfleet']) !!}
        <p class="text-danger">{{ $errors->first('subfleet_id') }}</p>
    </div>

    <div class="form-group col-sm-6">
        {!! Form::label('name', 'Name:') !!}&nbsp;<span class="required">*</span>
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
        <p class="text-danger">{{ $errors->first('name') }}</p>
    </div>
</div>

<div class="row">
    <div class="form-group col-sm-6">
        {!! Form::label('icao', 'ICAO:') !!}
        {!! Form::text('icao', null, ['class' => 'form-control']) !!}
        <p class="text-danger">{{ $errors->first('icao') }}</p>
    </div>

    <div class="form-group col-sm-6">
        {!! Form::label('zfw', 'Zero Fuel Weight:') !!}
        {!! Form::text('zfw', null, ['class' => 'form-control']) !!}
        <p class="text-danger">{{ $errors->first('zfw') }}</p>
    </div>
</div>
<div class="row">
    <!-- Registration Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('registration', 'Registration:') !!}
        {{--<p class="text-success small">Enter the registration with the country prefix</p>--}}
        {!! Form::text('registration', null, ['class' => 'form-control']) !!}
        <p class="text-danger">{{ $errors->first('registration') }}</p>
    </div>

    <!-- Active Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('status', 'Status:') !!}
        {!! Form::select('status', $statuses, null, ['class' => 'form-control select2', 'placeholder' => 'Select Status']) !!}
        <p class="text-danger">{{ $errors->first('subfleet_id') }}</p>
    </div>
</div>
<div class="row">
    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        <div class="pull-right">
            {!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
            <a href="{!! route('admin.aircraft.index') !!}" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>

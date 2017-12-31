<div class="row">
    <!-- Code Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('icao', 'Code:') !!}&nbsp;<span class="required">*</span>
        {!! Form::text('icao', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Name Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('name', 'Name:') !!}&nbsp;<span class="required">*</span>
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-6">
        {!! Form::label('iata', 'IATA:') !!}
        {!! Form::text('iata', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group col-sm-6">
        {!! Form::label('logo', 'Logo URL:') !!}
        {!! Form::text('logo', null, ['class' => 'form-control']) !!}
    </div>

</div>

<div class="row">
    <!-- Active Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('active', 'Active:') !!}
        <label class="checkbox-inline">
            {!! Form::hidden('active', 0, false) !!}
            {!! Form::checkbox('active', 1, null) !!}
        </label>
    </div>
</div>
<div class="row">
    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        <div class="pull-right">
            {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
            <a href="{!! route('admin.airlines.index') !!}" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>

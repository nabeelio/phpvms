<!-- ICAO Field -->
<div class="form-group col-sm-6">
    {!! Form::label('icao', 'ICAO:') !!} (<a class="small" href="https://www.icao.int/publications/DOC8643/Pages/Search.aspx" target="_blank">find</a>)
    {!! Form::text('icao', null, ['class' => 'form-control']) !!}
</div>

<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Registration Field -->
<div class="form-group col-sm-6">
    {!! Form::label('registration', 'Registration:') !!}
    {!! Form::text('registration', null, ['class' => 'form-control']) !!}
</div>

<!-- Tail Number Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tail_number', 'Tail Number:') !!}
    {!! Form::text('tail_number', null, ['class' => 'form-control']) !!}
</div>

<!-- Active Field -->
<div class="form-group col-sm-6">
    <div class="checkbox">
        <label class="checkbox-inline">
            {!! Form::hidden('active', false) !!}
            {!! Form::checkbox('active', '1', true) !!}
        {!! Form::label('active', 'Active') !!}
        </label>
    </div>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.aircraft.index') !!}" class="btn btn-default">Cancel</a>
</div>
<script>
jQuery('input#active').iCheck('check');
//$(document).ready(function() {
//})
</script>

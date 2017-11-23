<div class="row">
<div class="form-group col-sm-6">
    {!! Form::label('code', 'Code:') !!}
    {!! Form::text('code', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('price', 'Price:') !!}
    {!! Form::text('price', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('cost', 'Cost:') !!}
    {!! Form::text('cost', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('capacity', 'Capacity:') !!}
    {!! Form::text('capacity', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('notes', 'Notes:') !!}
    {!! Form::text('notes', null, ['class' => 'form-control']) !!}
</div>

<!-- Active Field -->
<div class="form-group col-sm-12">
    {!! Form::label('active', 'Active:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('active', 0, false) !!}
        {!! Form::checkbox('active', 1, null) !!}
    </label>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <div class="pull-right">
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('admin.fares.index') !!}" class="btn btn-default">Cancel</a>
    </div>
</div>
</div>

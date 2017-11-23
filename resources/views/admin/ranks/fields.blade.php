<div class="row">
<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Hours Field -->
<div class="form-group col-sm-6">
    {!! Form::label('hours', 'Hours:') !!}
    {!! Form::text('hours', null, ['class' => 'form-control']) !!}
</div>

<!-- Auto Approve Acars Field -->
<div class="form-group col-sm-4 text-center">
    <div class="checkbox">
        <label class="checkbox-inline">
            {!! Form::hidden('auto_approve_acars', false) !!}
            {!! Form::checkbox('auto_approve_acars', '1', true) !!}
            {!! Form::label('auto_approve_acars', 'Auto Approve ACARS') !!}
        </label>
    </div>
</div>

<!-- Auto Approve Manual Field -->
<div class="form-group col-sm-4 text-center">
    <div class="checkbox">
        <label class="checkbox-inline">
            {!! Form::hidden('auto_approve_manual', false) !!}
            {!! Form::checkbox('auto_approve_manual', '1', true) !!}
            {!! Form::label('auto_approve_manual', 'Auto Approve Manual') !!}
        </label>
    </div>
</div>

<!-- Auto Promote Field -->
<div class="form-group col-sm-4 text-center">
    <div class="checkbox">
        <label class="checkbox-inline">
            {!! Form::hidden('auto_promote', false) !!}
            {!! Form::checkbox('auto_promote', '1', true) !!}
            {!! Form::label('auto_promote', 'Auto Promote') !!}
        </label>
    </div>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12 text-right">
    <div class="pull-right">
        {!! Form::submit('Add', ['class' => 'btn btn-primary']) !!}
    </div>
</div>
</div>

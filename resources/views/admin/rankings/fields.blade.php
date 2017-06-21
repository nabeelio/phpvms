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
<div class="form-group col-sm-6">
    {!! Form::label('auto_approve_acars', 'Auto Approve Acars:') !!}
    {!! Form::text('auto_approve_acars', null, ['class' => 'form-control']) !!}
</div>

<!-- Auto Approve Manual Field -->
<div class="form-group col-sm-6">
    {!! Form::label('auto_approve_manual', 'Auto Approve Manual:') !!}
    {!! Form::text('auto_approve_manual', null, ['class' => 'form-control']) !!}
</div>

<!-- Auto Promote Field -->
<div class="form-group col-sm-6">
    {!! Form::label('auto_promote', 'Auto Promote:') !!}
    {!! Form::text('auto_promote', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.rankings.index') !!}" class="btn btn-default">Cancel</a>
</div>

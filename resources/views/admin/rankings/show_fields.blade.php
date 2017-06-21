<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $ranking->id !!}</p>
</div>

<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    <p>{!! $ranking->name !!}</p>
</div>

<!-- Hours Field -->
<div class="form-group">
    {!! Form::label('hours', 'Hours:') !!}
    <p>{!! $ranking->hours !!}</p>
</div>

<!-- Auto Approve Acars Field -->
<div class="form-group">
    {!! Form::label('auto_approve_acars', 'Auto Approve Acars:') !!}
    <p>{!! $ranking->auto_approve_acars !!}</p>
</div>

<!-- Auto Approve Manual Field -->
<div class="form-group">
    {!! Form::label('auto_approve_manual', 'Auto Approve Manual:') !!}
    <p>{!! $ranking->auto_approve_manual !!}</p>
</div>

<!-- Auto Promote Field -->
<div class="form-group">
    {!! Form::label('auto_promote', 'Auto Promote:') !!}
    <p>{!! $ranking->auto_promote !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $ranking->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $ranking->updated_at !!}</p>
</div>


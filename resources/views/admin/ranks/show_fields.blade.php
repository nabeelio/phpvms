<!-- Id Field -->
<div class="form-group">
  {{ Form::label('id', 'Id:') }}
  <p>{{ $rank->id }}</p>
</div>

<!-- Name Field -->
<div class="form-group">
  {{ Form::label('name', 'Name:') }}
  <p>{{ $rank->name }}</p>
</div>

<!-- Hours Field -->
<div class="form-group">
  {{ Form::label('hours', 'Hours:') }}
  <p>{{ $rank->hours }}</p>
</div>

<!-- Auto Approve Acars Field -->
<div class="form-group">
  {{ Form::label('auto_approve_acars', 'Auto Approve Acars:') }}
  <p>{{ $rank->auto_approve_acars }}</p>
</div>

<!-- Auto Approve Manual Field -->
<div class="form-group">
  {{ Form::label('auto_approve_manual', 'Auto Approve Manual:') }}
  <p>{{ $rank->auto_approve_manual }}</p>
</div>

<!-- Auto Promote Field -->
<div class="form-group">
  {{ Form::label('auto_promote', 'Auto Promote:') }}
  <p>{{ $rank->auto_promote }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
  {{ Form::label('created_at', 'Created At:') }}
  <p>{{ show_datetime($rank->created_at) }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
  {{ Form::label('updated_at', 'Updated At:') }}
  <p>{{ show_datetime($rank->updated_at) }}</p>
</div>


<!-- Id Field -->
<!-- Airline Id Field -->
<div class="form-group">
  {{ Form::label('airline_id', 'Airline Id:') }}
  <p>{{ $subfleet->airline->name }}</p>
</div>

<!-- Name Field -->
<div class="form-group">
  {{ Form::label('name', 'Name:') }}
  <p>{{ $subfleet->name }}</p>
</div>

<!-- Type Field -->
<div class="form-group">
  {{ Form::label('type', 'Type:') }}
  <p>{{ $subfleet->type }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
  {{ Form::label('created_at', 'Created At:') }}
  <p>{{ show_datetime($subfleet->created_at) }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
  {{ Form::label('updated_at', 'Updated At:') }}
  <p>{{ show_datetime($subfleet->updated_at) }}</p>
</div>


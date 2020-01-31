<!-- Id Field -->
<div class="form-group">
  {{ Form::label('id', 'Id:') }}
  <p>{{ $field->id }}</p>
</div>

<!-- Name Field -->
<div class="form-group">
  {{ Form::label('name', 'Name:') }}
  <p>{{ $field->name }}</p>
</div>

<!-- Required Field -->
<div class="form-group">
  {{ Form::label('required', 'Required:') }}
  <p>{{ $field->required }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
  {{ Form::label('created_at', 'Created At:') }}
  <p>{{ show_datetime($field->created_at) }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
  {{ Form::label('updated_at', 'Updated At:') }}
  <p>{{ show_datetime($field->updated_at) }}</p>
</div>


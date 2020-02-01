<!-- Code Field -->
<div class="form-group">
  {{ Form::label('icao', 'ICAO:') }}
  <p>{{ $airlines->icao }}</p>
</div>

<div class="form-group">
  {{ Form::label('iata', 'IATA:') }}
  <p>{{ $airlines->iata }}</p>
</div>

<!-- Name Field -->
<div class="form-group">
  {{ Form::label('name', 'Name:') }}
  <p>{{ $airlines->name }}</p>
</div>

<div class="form-group">
  {{ Form::label('logo', 'Logo URL:') }}
  <p>{{ $airlines->logo }}</p>
</div>

<!-- Active Field -->
<div class="form-group">
  {{ Form::label('active', 'Active:') }}
  <p>{{ $airlines->active }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
  {{ Form::label('created_at', 'Created At:') }}
  <p>{{ show_datetime($airlines->created_at) }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
  {{ Form::label('updated_at', 'Updated At:') }}
  <p>{{ show_datetime($airlines->updated_at) }}</p>
</div>


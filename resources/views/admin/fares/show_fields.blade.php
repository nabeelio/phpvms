<!-- Code Field -->
<div class="form-group">
  {{ Form::label('code', 'Code:') }}
  <p>{{ $fare->code }}</p>
</div>

<!-- Name Field -->
<div class="form-group">
  {{ Form::label('name', 'Name:') }}
  <p>{{ $fare->name }}</p>
</div>

<!-- Price Field -->
<div class="form-group">
  {{ Form::label('price', 'Price:') }}
  <p>{{ $fare->price }}</p>
</div>

<!-- Cost Field -->
<div class="form-group">
  {{ Form::label('cost', 'Cost:') }}
  <p>{{ $fare->cost }}</p>
</div>

<!-- Notes Field -->
<div class="form-group">
  {{ Form::label('notes', 'Notes:') }}
  <p>{{ $fare->notes }}</p>
</div>

<!-- Active Field -->
<div class="form-group">
  {{ Form::label('active', 'Active:') }}
  <p>{{ $fare->active }}</p>
</div>

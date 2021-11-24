<!-- Id Field -->
<div class="form-group">
  {{ Form::label('id', 'Id:') }}
  <p>{{ $typerating->id }}</p>
</div>

<!-- Name Field -->
<div class="form-group">
  {{ Form::label('name', 'Name:') }}
  <p>{{ $typerating->name }}</p>
</div>

<!-- Type Code Field -->
<div class="form-group">
  {{ Form::label('type', 'Type Code:') }}
  <p>{{ $typerating->type }}</p>
</div>

<!-- Description Field -->
<div class="form-group">
  {{ Form::label('description', 'Description:') }}
  <p>{{ $typerating->description }}</p>
</div>

<!-- Image URL Field -->
<div class="form-group">
  {{ Form::label('image_url', 'Image URL:') }}
  <p>{{ $typerating->image_url }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
  {{ Form::label('created_at', 'Created At:') }}
  <p>{{ show_datetime($typerating->created_at) }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
  {{ Form::label('updated_at', 'Updated At:') }}
  <p>{{ show_datetime($typerating->updated_at) }}</p>
</div>


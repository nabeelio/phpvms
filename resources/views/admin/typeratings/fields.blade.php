<div class="row">
  <div class="form-group col-sm-7">
    {{ Form::label('name', 'Name:') }}
    {{ Form::text('name', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('name') }}</p>
  </div>
  <div class="form-group col-sm-5">
    {{ Form::label('type', 'Type Code:') }}
    {{ Form::text('type', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('type') }}</p>
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-7">
    {{ Form::label('description', 'Description:') }}
    {{ Form::text('description', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('description') }}</p>
  </div>
  <div class="form-group col-sm-5">
    {{ Form::label('image_url', 'Image Link:') }}
    {{ Form::text('image_url', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('image_url') }}</p>
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-3">
    <div class="checkbox">
      <label class="checkbox-inline">
        {{ Form::label('active', 'Active:') }}
        {{ Form::hidden('active', false) }}
        {{ Form::checkbox('active') }}
      </label>
    </div>
  </div>
  <div class="col-sm-9">
    <div class="text-right">
      {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
    </div>
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-6">
    {{ Form::label('name', 'Name:') }}&nbsp;&nbsp;<span class="required">*</span>
    {{ Form::text('name', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('name') }}</p>
  </div>
</div>
<div class="row">
  <!-- Submit Field -->
  <div class="form-group col-sm-12">
    {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
    <a href="{{ route('admin.flightfields.index') }}" class="btn btn-default">Cancel</a>
  </div>
</div>

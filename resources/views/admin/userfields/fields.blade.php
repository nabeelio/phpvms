<div class="row">
  <div class="form-group col-sm-6">
    {{ Form::label('name', 'Name:') }}&nbsp;&nbsp;<span class="required">*</span>
    {{ Form::text('name', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('name') }}</p>
  </div>

  <div class="form-group col-sm-6">
    {{ Form::label('description', 'Description:') }}
    {{ Form::text('description', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('description') }}</p>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="checkbox">
      <label class="checkbox-inline">
        {{ Form::label('required', 'Required:') }}
        <input name="required" type="hidden" value="0" />
        {{ Form::checkbox('required') }}
      </label>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="checkbox">
      <label class="checkbox-inline">
        {{ Form::label('show_on_registration', 'Show On Registration:') }}
        <input name="show_on_registration" type="hidden" value="0" />
        {{ Form::checkbox('show_on_registration') }}
      </label>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="checkbox">
      <label class="checkbox-inline">
        {{ Form::label('private', 'Private (only visible to admins):') }}
        <input name="private" type="hidden" value="0" />
        {{ Form::checkbox('private') }}
      </label>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="checkbox">
        <label class="checkbox-inline">
          {{ Form::label('active', 'Active:') }}
          <input name="active" type="hidden" value="0" />
          {{ Form::checkbox('active') }}
        </label>
      </div>
    </div>
</div>
<div class="row">
  <!-- Submit Field -->
  <div class="form-group col-sm-12">
    {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
    <a href="{{ route('admin.userfields.index') }}" class="btn btn-default">Cancel</a>
  </div>
</div>

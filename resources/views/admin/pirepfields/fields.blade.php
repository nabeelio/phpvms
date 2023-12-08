<div class="row">
  <div class="form-group col-sm-4">
    {{ Form::label('name', 'Name:') }}&nbsp;&nbsp;<span class="required">*</span>
    {{ Form::text('name', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('name') }}</p>
  </div>
  <div class="form-group col-sm-4">
    {{ Form::label('description', 'Desc:') }}
    {{ Form::text('description', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('description') }}</p>
  </div>
  <div class="form-group col-sm-2">
    {{ Form::label('required', 'Required:') }}
    {{ Form::hidden('required', 0) }}
    {{ Form::checkbox('required', null) }}
  </div>
  <div class="form-group col-sm-2">
    {{ Form::label('pirep_source', 'Pirep Source:') }}
    {{ Form::select('pirep_source', \App\Models\Enums\PirepFieldSource::select(), null, ['class' => 'form-control select2', 'style' => 'width: 100%']) }}
  </div>
</div>
<div class="row">
  <div class="form-group col-sm-12">
    {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
    <a href="{{ route('admin.pirepfields.index') }}" class="btn btn-default">Cancel</a>
  </div>
</div>

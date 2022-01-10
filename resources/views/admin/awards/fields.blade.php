<div class="row">
  <div class="col-sm-12">
    @component('admin.components.info')
      These are the awards that pilots can earn. Each award is assigned an
      award class, which will be run whenever a pilot's stats are changed,
      including after a PIREP is accepted.
    @endcomponent
  </div>
</div>
<div class="row">
  <div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}&nbsp;<span class="required">*</span>
    <div class="callout callout-info">
      <i class="icon fa fa-info">&nbsp;&nbsp;</i>
      This will be the title of the award
    </div>
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
    <p class="text-danger">{{ $errors->first('name') }}</p>
  </div>

  <div class="form-group col-sm-6">
    {!! Form::label('image', 'Image:') !!}
    <div class="callout callout-info">
      <i class="icon fa fa-info">&nbsp;&nbsp;</i>
      This is the image of the award. Be creative!
    </div>
    {!! Form::text('image_url', null, ['class' => 'form-control', 'placeholder' => 'Enter the url of the image location']) !!}
    <p class="text-danger">{{ $errors->first('image_url') }}</p>
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-6">
    {!! Form::label('description', 'Description:') !!}&nbsp
    <div class="callout callout-info">
      <i class="icon fa fa-info">&nbsp;&nbsp;</i>
      This is the description of the award.
    </div>
    {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
    <p class="text-danger">{{ $errors->first('description') }}</p>
  </div>

  <div class="form-group col-sm-6">
    <div>
      {{ Form::label('ref_model', 'Award Class:') }}
      {{ Form::select('ref_model', $award_classes, null, ['class' => 'form-control select2', 'id' => 'award_class_select']) }}
      <p class="text-danger">{{ $errors->first('ref_model') }}</p>
    </div>
    <div>
      {{ Form::label('ref_model_params', 'Award Class parameters') }}
      {{ Form::text('ref_model_params', null, ['class' => 'form-control']) }}
      <p class="text-danger">{{ $errors->first('ref_model_params') }}</p>
      <p id="ref_model_param_description"></p>
    </div>
  </div>
</div>

<div class="row">
  {{-- Active/Deactive Checkbox --}}
  <div class="form-group col-sm-6 text-left">
    <div class="checkbox">
      <label class="checkbox-inline">
        {{ Form::label('active', 'Active: ') }}
        {{ Form::hidden('active', false) }}
        {{ Form::checkbox('active') }}
      </label>
    </div>
  </div>
  {{-- Form Actions --}}
  <div class="form-group col-sm-6">
    <div class="pull-right">
      {!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
      <a href="{!! route('admin.awards.index') !!}" class="btn btn-warn">Cancel</a>
    </div>
  </div>
</div>

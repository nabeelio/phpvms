<div class="row">
  <div class="col-12">
    <div class="form-container">
      <h6><i class="fas fa-info-circle"></i>
        Download Information
      </h6>
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      <div class="form-container-body">
        <div class="row">
          <!-- DOWNLOAD Field -->
          <div class="form-group col-sm-5">
            {{ Form::label('name', 'Download Name') }}&nbsp;<span class="required">*</span>
            {{ Form::text('name', null, ['class' => 'form-control']) }}
            <p class="text-danger">{{ $errors->first('name') }}</p>
          </div>

          <!-- Download Model Field -->
          <div class="form-group col-sm-5">
            {{ Form::label('ref_model', 'Download Type:') }}&nbsp;<span class="required">*</span>
            {{ Form::select('ref_model', $ref_models, null, ['class' => 'form-control select2', 'id' => 'ref_model']) }}
            <p class="text-danger">{{ $errors->first('ref_model') }}</p>
          </div>

        </div>
        <hr>
        <h5>Select Download For : </h5>
        {{-- NEXT ROW --}}
        <div id="ref_models" class="row">
          <!-- Download Model ID Field -->
          <div class="form-group col-sm-4">
            {{ Form::label('ref_model_id', 'Download For Airport:') }}&nbsp;<span class="required">*</span>
            {{
                Form::select('ref_model_id', $ref_airports, null, [
                      'class'       => 'form-control select2',
                      'disabled'    => 'disabled',
                      'id'          => 'ref_model_Airport'
                ])
            }}
            <p class="text-danger">{{ $errors->first('ref_model_id') }}</p>
          </div>

          <!-- Download Model ID Field -->
          <div class="form-group col-sm-4">
            {{ Form::label('ref_model_id', 'Download For Aircraft:') }}&nbsp;<span class="required">*</span>
            {{
                Form::select('ref_model_id', $ref_aircrafts, null, [
                      'class' => 'form-control select2',
                      'disabled' => 'disabled',
                      'id' => 'ref_model_Aircraft'
                ])
            }}
            <p class="text-danger">{{ $errors->first('ref_model_id') }}</p>
          </div>
        </div>

        {{-- NEXT ROW --}}

        <div class="row">
          <div class="form-group col-sm-6">
            {{ Form::label('description', 'Description :') }}
            {{ Form::textarea('description', null, ['class' => 'form-control']) }}
          </div>
          <div class="form-group col-sm-6">
            {{ Form::label('file', 'Select Download File : ') }}
            {{ Form::hidden('url', null) }}
            {{ Form::file('file', ['class' => 'form-control']) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="text-right">
      {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-info']) }}
    </div>
  </div>
</div>

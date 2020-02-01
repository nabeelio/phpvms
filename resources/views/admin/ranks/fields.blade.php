<div class="row">
  <!-- Name Field -->
  <div class="form-group col-sm-6">
    {{ Form::label('name', 'Name:') }}
    {{ Form::text('name', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('name') }}</p>
  </div>

  <div class="form-group col-md-6">
    {{ Form::label('image_url', 'Image Link:') }}
    {{ Form::text('image_url', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('image_url') }}</p>
  </div>
</div>
<div class="row">
  <!-- Hours Field -->
  <div class="form-group col-sm-4">
    {{ Form::label('hours', 'Hours:') }}
    {{ Form::number('hours', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('hours') }}</p>
  </div>

  <div class="form-group col-md-4">
    {{ Form::label('acars_base_pay_rate', 'ACARS Base Pay Rate:') }}
    {{ Form::number('acars_base_pay_rate', null, ['min' => 0, 'class' => 'form-control', 'step' => '0.01']) }}
    <p class="text-danger">{{ $errors->first('acars_base_pay_rate') }}</p>
    @component('admin.components.info')
      Base rate, per-flight hour, for ACARS PIREPs.
      Can be adjusted via a multiplier on the subfleet.
    @endcomponent
  </div>

  <div class="form-group col-md-4">
    {{ Form::label('manual_base_pay_rate', 'Manual Base Pay Rate:') }}
    {{ Form::number('manual_base_pay_rate', null, ['min' => 0, 'class' => 'form-control', 'step' => '0.01']) }}
    <p class="text-danger">{{ $errors->first('manual_base_pay_rate') }}</p>
    @component('admin.components.info')
      Base rate, per-flight hour, for manually-filed PIREPs.
      Can be adjusted via a multiplier on the subfleet.
    @endcomponent
  </div>

</div>
<div class="row">
  <div class="col-12">
    <div class="form-container">
      <h6><i class="fas fa-check-square"></i>
        &nbsp;Options
      </h6>
      <div class="form-container-body">
        <div class="row">
          <!-- Auto Approve Acars Field -->
          <div class="form-group col-sm-4">
            <div class="checkbox">
              <label class="checkbox-inline">
                {{ Form::hidden('auto_approve_acars', false) }}
                {{ Form::checkbox('auto_approve_acars') }}
                {{ Form::label('auto_approve_acars', 'Auto Approve ACARS PIREPs') }}
              </label>
              <div style="margin-left: 10px">
                @component('admin.components.info')
                  PIREPS submitted through ACARS are automatically accepted
                @endcomponent
              </div>
            </div>
          </div>

          <!-- Auto Approve Manual Field -->
          <div class="form-group col-sm-4">
            <div class="checkbox">
              <label class="checkbox-inline">
                {{ Form::hidden('auto_approve_manual', false) }}
                {{ Form::checkbox('auto_approve_manual') }}
                {{ Form::label('auto_approve_manual', 'Auto Approve Manual PIREPs') }}
              </label>
              <div style="margin-left: 10px">
                @component('admin.components.info')
                  PIREPS submitted manually are automatically accepted
                @endcomponent
              </div>
            </div>
          </div>

          <!-- Auto Promote Field -->
          <div class="form-group col-sm-4">
            <div class="checkbox">
              <label class="checkbox-inline">
                {{ Form::hidden('auto_promote', false) }}
                {{ Form::checkbox('auto_promote') }}
                {{ Form::label('auto_promote', 'Auto Promote') }}
              </label>
              <div style="margin-left: 10px">
                @component('admin.components.info')
                  When a pilot reaches these hours, they'll be upgraded to this rank
                @endcomponent
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <div class="text-right">
      {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
    </div>
  </div>
</div>

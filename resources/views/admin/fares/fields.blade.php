<div class="form-container">
  <h6><i class="fas fa-info-circle"></i>
    &nbsp;Fare Information
  </h6>
  <div class="form-container-body">
    <div class="row">
      <div class="col-md-12">
        <div class="callout callout-success">
          When a fare is assigned to a subfleet, the price, cost and capacity can be overridden,
          so you can create default values that will apply to most of your subfleets, and change
          them where they will differ.
        </div>
        <br/>
      </div>
    </div>
    <div class="row">
      <div class="form-group col-sm-4">
        {{ Form::label('code', 'Code:') }}&nbsp;<span class="required">*</span>
        @component('admin.components.info')
          How this fare class will show up on a ticket
        @endcomponent
        {{ Form::text('code', null, ['class' => 'form-control']) }}
        <p class="text-danger">{{ $errors->first('code') }}</p>
      </div>

      <div class="form-group col-sm-4">
        {{ Form::label('name', 'Name:') }}&nbsp;<span class="required">*</span>
        @component('admin.components.info')
          The fare class name, E.g, "Economy" or "First"
        @endcomponent
        {{ Form::text('name', null, ['class' => 'form-control']) }}
        <p class="text-danger">{{ $errors->first('name') }}</p>
      </div>

      <div class="form-group col-sm-4">
        {{ Form::label('type', 'Fare Type:') }}&nbsp;<span class="required">*</span>
        @component('admin.components.info')
          If this is a passenger or cargo fare
        @endcomponent
        {{ Form::select('type', $fare_types, null , [
              'id'    => 'type',
              'class' => 'form-control select2'
          ]) }}
        <p class="text-danger">{{ $errors->first('type') }}</p>
      </div>

    </div>
  </div>
  </div>
<div class="form-container">
    <h6><i class="fas fa-info-circle"></i>
      &nbsp;Base Fare Finances
    </h6>
    <div class="form-container-body">
      <div class="row">

        <div class="form-group col-sm-6">
          {{ Form::label('price', 'Price:') }}
          @component('admin.components.info')
            This is the price of a ticket or price per {{ setting('units.weight') }}
          @endcomponent
          {{ Form::text('price', null, ['class' => 'form-control', 'placeholder' => 0]) }}
          <p class="text-danger">{{ $errors->first('price') }}</p>
        </div>

        <div class="form-group col-sm-6">
          {{ Form::label('cost', 'Cost:') }}
          @component('admin.components.info')
            The operating cost per unit (passenger or {{ setting('units.weight') }})
          @endcomponent
          {{ Form::number('cost', null, ['class' => 'form-control', 'placeholder' => 0, 'step' => '0.01']) }}
          <p class="text-danger">{{ $errors->first('cost') }}</p>
        </div>
      </div>
      <div class="row">
        <div class="form-group col-sm-6">
          {{ Form::label('capacity', 'Capacity:') }}
          @component('admin.components.info')
            Max seats or capacity available. This can be adjusted in the subfleet
          @endcomponent
          {{ Form::number('capacity', null, ['class' => 'form-control', 'min' => 0]) }}
          <p class="text-danger">{{ $errors->first('capacity') }}</p>
        </div>

        <div class="form-group col-sm-6">
          {{ Form::label('notes', 'Notes:') }}
          @component('admin.components.info')
            Notes for this fare
          @endcomponent
          {{ Form::text('notes', null, ['class' => 'form-control']) }}
          <p class="text-danger">{{ $errors->first('notes') }}</p>
        </div>

      </div>
    </div>
</div>
<div class="row">
  <!-- Active Field -->
  <div class="form-group col-sm-12">
    {{ Form::label('active', 'Active:') }}
    <label class="checkbox-inline">
      {{ Form::hidden('active', 0, false) }}
      {{ Form::checkbox('active', 1, null) }}
    </label>
  </div>
</div>
<div class="row">
  <!-- Submit Field -->
  <div class="form-group col-sm-12">
    <div class="pull-right">
      {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-info']) }}
    </div>
  </div>
</div>

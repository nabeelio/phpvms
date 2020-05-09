<div class="row">
  <!-- Code Field -->
  <div class="form-group col-sm-4">
    {{ Form::label('airline_id', 'Airline:') }}

    {{ Form::select('airline_id', $airlines_list, null , ['class' => 'form-control select2']) }}
    <p class="text-danger">{{ $errors->first('airline_id') }}</p>
    @component('admin.components.info')
      If an airline is selected, then the expense will only be applied
      to the selected airline, or flights in that airline.
    @endcomponent
  </div>

  <div class="form-group col-sm-4">
    {{ Form::label('type', 'Expense Type:') }}&nbsp;<span class="required">*</span>
    {{ Form::select('type', $expense_types, null , ['class' => 'form-control select2']) }}
    <p class="text-danger">{{ $errors->first('type') }}</p>
  </div>

  <div class="form-group col-sm-4">
    {{ Form::label('flight_type', 'Flight Types:') }}&nbsp;
    {{ Form::select('flight_type[]', $flight_types, null , ['class' => 'form-control select2', 'multiple']) }}
    <p class="text-danger">{{ $errors->first('type') }}</p>
    @component('admin.components.info')
      If selected and the expense type is "flight", this expense will only apply to the specified flight types
    @endcomponent
  </div>
</div>
<div class="row">
  <div class="form-group col-sm-6">
    {{ Form::label('name', 'Expense Name:') }}
    {{ Form::text('name', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('name') }}</p>
  </div>

  <div class="form-group col-sm-6">
    {{ Form::label('amount', 'Amount:') }}
    {{ Form::number('amount', null, ['class' => 'form-control', 'min' => 0, 'step' => '0.01']) }}
    <p class="text-danger">{{ $errors->first('amount') }}</p>
  </div>

</div>

<div class="row">

  <div class="col-sm-5">
    {{ Form::label('multiplier', 'Multiplier:') }}
    <label class="checkbox-inline">
      {{ Form::hidden('multiplier', 0, false) }}
      {{ Form::checkbox('multiplier', 1, null) }}
    </label>
    @component('admin.components.info')
      If checked, with a PIREP, this expense can be modified by a multiplier
      on the subfleet. This is ignored for daily and monthly expenses
    @endcomponent
  </div>

  <div class="col-sm-3">
    {{ Form::label('active', 'Active:') }}
    <label class="checkbox-inline">
      {{ Form::hidden('active', 0, false) }}
      {{ Form::checkbox('active', 1, null) }}
    </label>
  </div>

  <div class="form-group col-sm-4">
    <div class="pull-right">
      {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
      <a href="{{ route('admin.expenses.index') }}" class="btn btn-default">Cancel</a>
    </div>
  </div>
</div>

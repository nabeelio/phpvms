<div class="row">
    <div class="col-md-12">
        <div class="callout callout-success">
        When a fare is assigned to a subfleet, the price, cost and capacity can be overridden,
        so you can create default values that will apply to most of your subfleets, and change
        them where they will differ.
        </div>
        <br />
    </div>
</div>
<div class="row">
<div class="form-group col-sm-6">
    {{ Form::label('code', 'Code:') }}&nbsp;<span class="required">*</span>
    @component('admin.components.info')
        How this fare class will show up on a ticket
    @endcomponent
    {{ Form::text('code', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('code') }}</p>
</div>

<div class="form-group col-sm-6">
    {{ Form::label('name', 'Name:') }}&nbsp;<span class="required">*</span>
    @component('admin.components.info')
        The fare class name, E.g, "Economy" or "First"
    @endcomponent
    {{ Form::text('name', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('name') }}</p>
</div>

<div class="form-group col-sm-6">
    {{ Form::label('price', 'Price:') }}
    @component('admin.components.info')
        This is the price of a ticket for a passenger
    @endcomponent
    {{ Form::text('price', null, ['class' => 'form-control', 'placeholder' => 0]) }}
    <p class="text-danger">{{ $errors->first('price') }}</p>
</div>

<div class="form-group col-sm-6">
    {{ Form::label('cost', 'Cost:') }}
    @component('admin.components.info')
        The operating cost
    @endcomponent
    {{ Form::text('cost', null, ['class' => 'form-control', 'placeholder' => 0]) }}
    <p class="text-danger">{{ $errors->first('cost') }}</p>
</div>

<div class="form-group col-sm-6">
    {{ Form::label('capacity', 'Capacity:') }}
    @component('admin.components.info')
        The number of seats available in this class.
    @endcomponent
    {{ Form::text('capacity', null, ['class' => 'form-control', 'placeholder' => 0]) }}
    <p class="text-danger">{{ $errors->first('capacity') }}</p>
</div>

<div class="form-group col-sm-6">
    {{ Form::label('notes', 'Notes:') }}
    {{ Form::text('notes', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('notes') }}</p>
</div>

<!-- Active Field -->
<div class="form-group col-sm-12">
    {{ Form::label('active', 'Active:') }}
    <label class="checkbox-inline">
        {{ Form::hidden('active', 0, false) }}
        {{ Form::checkbox('active', 1, null) }}
    </label>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <div class="pull-right">
        {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
        <a href="{{ route('admin.fares.index') }}" class="btn btn-warn">Cancel</a>
    </div>
</div>
</div>

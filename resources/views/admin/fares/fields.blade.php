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
    {!! Form::label('code', 'Code:') !!}&nbsp;<span class="required">*</span>
    <div class="callout callout-info">
        <i class="icon fa fa-info">&nbsp;&nbsp;</i>
        How this fare class will show up on a ticket
    </div>
    {!! Form::text('code', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}&nbsp;<span class="required">*</span>
    <div class="callout callout-info">
        <i class="icon fa fa-info">&nbsp;&nbsp;</i>
        The fare class name, E.g, "Economy" or "First"
    </div>
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('price', 'Price:') !!}
    <div class="callout callout-info">
        <i class="icon fa fa-info">&nbsp;&nbsp;</i>
        This is the price of a ticket for a passenger
    </div>
    {!! Form::text('price', null, ['class' => 'form-control', 'placeholder' => 0]) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('cost', 'Cost:') !!}
    <div class="callout callout-info">
        <i class="icon fa fa-info">&nbsp;&nbsp;</i>
        The operating cost
    </div>
    {!! Form::text('cost', null, ['class' => 'form-control', 'placeholder' => 0]) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('capacity', 'Capacity:') !!}
    <div class="callout callout-info">
        <i class="icon fa fa-info">&nbsp;&nbsp;</i>
        The number of seats available in this class.
    </div>
    {!! Form::text('capacity', null, ['class' => 'form-control', 'placeholder' => 0]) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('notes', 'Notes:') !!}
    <div class="callout callout-info">
        &nbsp;
    </div>
    {!! Form::text('notes', null, ['class' => 'form-control']) !!}
</div>

<!-- Active Field -->
<div class="form-group col-sm-12">
    {!! Form::label('active', 'Active:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('active', 0, false) !!}
        {!! Form::checkbox('active', 1, null) !!}
    </label>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <div class="pull-right">
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('admin.fares.index') !!}" class="btn btn-default">Cancel</a>
    </div>
</div>
</div>

<div class="row">
    <div class="form-group col-sm-6">
        {!! Form::label('airline_id', 'Airline:') !!}
        {!! Form::select('airline_id', $airlines, null , ['class' => 'form-control select2']) !!}
    </div>

    <div class="form-group col-sm-6">
        {!! Form::label('name', 'Name:') !!}
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group col-sm-6">
        {!! Form::label('type', 'Type:') !!}
        {!! Form::text('type', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group col-sm-6">
        {!! Form::label('fuel_type', 'Fuel Type:') !!}
        {!! Form::select('fuel_type', $fuel_types, null , ['class' => 'form-control select2']) !!}
    </div>

    <div class="form-group col-sm-12">
        <div class="pull-right">
            {!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
            <a href="{!! route('admin.subfleets.index') !!}" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>

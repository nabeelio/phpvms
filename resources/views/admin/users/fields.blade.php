<div class="row">
    <div class="form-group col-sm-4">
        {!! Form::label('name', 'Name:') !!}
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group col-sm-4">
        {!! Form::label('email', 'Email:') !!}
        {!! Form::text('email', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group col-sm-4">
        {!! Form::label('timezone', 'Timezone:') !!}
        {!! Form::select('timezone', $timezones, null, ['id'    => 'timezone', 'class' => 'select2' ]); !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-sm-6">
        {!! Form::label('password', 'Password:') !!}
        {!! Form::password('password', ['class' => 'form-control']) !!}
    </div>
    <div class="form-group col-sm-6">
        {!! Form::label('home_airport_id', 'Home Airport:') !!}
        {!! Form::select('home_airport_id', $airports, null , ['class' => 'form-control select2']) !!}
        <br /><br />
        {!! Form::label('curr_airport_id', 'Current Airport:') !!}
        {!! Form::select('curr_airport_id', $airports, null , ['class' => 'form-control select2']) !!}
    </div>

</div>

<div class="row">
    <div class="form-group col-sm-4">
        {!! Form::label('airline_id', 'Airline:') !!}
        {!! Form::select('airline_id', $airlines, null, ['class' => 'form-control select2', 'placeholder' => 'Select Airline']) !!}
    </div>

    <div class="form-group col-sm-4">
        {!! Form::label('rank_id', 'Rank:') !!}
        {!! Form::select('rank_id', $ranks, null, ['class' => 'form-control select2', 'placeholder' => 'Select Rank']) !!}
    </div>

    <div class="form-group col-sm-4">
        {!! Form::label('roles', 'Roles:') !!}
        {!! Form::select('roles[]', $roles, $user->roles->pluck('id'),
            ['class' => 'form-control select2', 'placeholder' => 'Select Roles', 'multiple']) !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-sm-6">
        {!! Form::label('state', 'State:') !!}
        <label class="checkbox-inline">
            {!! Form::select('state', UserState::labels(), null, ['class' => 'form-control select2']) !!}
        </label>
    </div>

    <!-- Submit Field -->
    <div class="form-group col-sm-6">
        <div class="pull-right">
            {!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
            <a href="{!! route('admin.users.index') !!}" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>

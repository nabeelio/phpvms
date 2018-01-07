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
    <div class="form-group col-sm-6 text-right">
        <a href="{!! route('admin.users.regen_apikey', ['id' => $user->id]) !!}" class="btn btn-warning"
           onclick="return confirm('Are you sure? This will reset this user\'s API key.')">new api key</a>
        &nbsp;
        {!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
        <a href="{!! route('admin.users.index') !!}" class="btn btn-default">Cancel</a>
    </div>
</div>

<div class="row">
    <div class="form-group col-sm-12">
        <table class="table table-hover">
            <tr>
                <td>API Key</td>
                <td>{!! $user->api_key !!}</td>
            </tr>
            <tr>
                <td>Total Flights</td>
                <td>{!! $user->flights !!}</td>
            </tr>
            <tr>
                <td>Flight Time</td>
                <td>{!! Utils::minutesToTimeString($user->flight_time) !!}</td>
            </tr>
            <tr>
                <td>IP Address</td>
                <td>{!! $user->last_ip !!}</td>
            </tr>
            <tr>
                <td>Registered On</td>
                <td>{!! show_datetime($user->created_at) !!}</td>
            </tr>
            <tr>
                <td>Last Login</td>
                <td>{!! show_datetime($user->updated_at) !!}</td>
            </tr>
        </table>
    </div>
</div>

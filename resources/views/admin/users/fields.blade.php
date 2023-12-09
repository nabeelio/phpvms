<div class="row">
  <div class="form-group col-sm-1">
    {{ Form::label('id', 'ID:') }}
    {{ Form::number('id', null, ['class' => 'form-control', 'readonly' => 'readonly']) }}
  </div>
  <div class="form-group col-sm-3">
    {{ Form::label('name', 'Name:') }}
    {{ Form::text('name', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
    <p class="text-danger">{{ $errors->first('name') }}</p>
  </div>
  <div class="form-group col-sm-2">
    {{ Form::label('email', 'Email:') }}
    {{ Form::text('email', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
    <p class="text-danger">{{ $errors->first('email') }}</p>
  </div>
  <div class="form-group col-sm-2">
    {{ Form::label('password', 'Password:') }}
    {{ Form::password('password', ['class' => 'form-control', 'autocomplete' => 'off']) }}
    <p class="text-danger">{{ $errors->first('password') }}</p>
  </div>
  <div class="form-group col-sm-2">
    {{ Form::label('country', 'Country:') }} <br/>
    {{ Form::select('country', $countries, null, ['class' => 'form-control select2' ]) }}
    <p class="text-danger">{{ $errors->first('country') }}</p>
  </div>
  <div class="form-group col-sm-2">
    {{ Form::label('timezone', 'Timezone:') }} <br/>
    {{ Form::select('timezone', $timezones, null, ['id' => 'timezone', 'class' => 'form-control select2' ]) }}
    <p class="text-danger">{{ $errors->first('timezone') }}</p>
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-1">
    {{ Form::label('pilot_id', 'Ident:') }}
    {{ Form::number('pilot_id', null, ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('pilot_id') }}</p>
  </div>
  <div class="form-group col-sm-1">
    {{ Form::label('callsign', 'Callsign:') }}
    {{ Form::text('callsign', null, ['class' => 'form-control', 'autocomplete' => 'off', 'maxlength' => 4]) }}
    <p class="text-danger">{{ $errors->first('callsign') }}</p>
  </div>
  <div class="form-group col-sm-1">
    {{ Form::label('transfer_time', 'Transfer Hours:') }}
    {{ Form::text('transfer_time', \App\Support\Units\Time::minutesToHours($user->transfer_time), ['class' => 'form-control']) }}
    <p class="text-danger">{{ $errors->first('transfer_time') }}</p>
  </div>
  <div class="form-group col-sm-3">
    {{ Form::label('airline_id', 'Airline:') }}
    {{ Form::select('airline_id', $airlines, null, ['class' => 'form-control select2', 'placeholder' => 'Select Airline']) }}
  </div>
  <div class="form-group col-sm-3">
    {{ Form::label('rank_id', 'Rank:') }}
    {{ Form::select('rank_id', $ranks, null, ['class' => 'form-control select2', 'placeholder' => 'Select Rank']) }}
  </div>
  <div class="form-group col-md-3">
    {{ Form::label('state', 'State:') }}
    {{ Form::select('state', UserState::labels(), null, ['class' => 'form-control select2', 'style' => 'width: 100%;']) }}
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-3">
    {{ Form::label('home_airport_id', 'Home Airport:') }}
    {{ Form::select('home_airport_id', $airports, null , ['class' => 'form-control airport_search']) }}
    <p class="text-danger">{{ $errors->first('home_airport_id') }}</p>
  </div>
  <div class="form-group col-sm-3">
    {{ Form::label('curr_airport_id', 'Current Airport:') }}
    {{ Form::select('curr_airport_id', $airports, null , ['class' => 'form-control airport_search']) }}
    <p class="text-danger">{{ $errors->first('curr_airport_id') }}</p>
  </div>
  <div class="form-group col-sm-6">
    @ability('admin', 'admin-user')
      {{ Form::label('roles', 'Roles:') }}
      {{ Form::select('roles[]', $roles, $user->roles->pluck('id'), ['class' => 'form-control select2', 'placeholder' => 'Select Roles', 'multiple']) }}
    @endability
  </div>
</div>

<div class="row">
  <div class="form-group col-md-12">
    {{ Form::label('notes', 'Management Notes:') }}
    {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 4, 'autocomplete' => 'off']) }}
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-12 text-right">
    {{-- <a href="{{ route('admin.users.regen_apikey', [$user->id]) }}" class="btn btn-warning" onclick="return confirm('Are you sure? This will reset this user\'s API key.')">New API Key</a> --}}
    &nbsp;
    @if (!$user->email_verified_at)
      <a href="{{ route('admin.users.verify_email', [$user->id]) }}" class="btn btn-danger">Verify email manually</a>
    @else
      <a href="{{ route('admin.users.request_email_verification', [$user->id]) }}" class="btn btn-warning">Request new email verification</a>
    @endif

    {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
    <a href="{{ route('admin.users.index') }}" class="btn btn-default">Cancel</a>
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-6">
    @if($user->fields)
      <table class="table table-hover">
        <tr>
          <td colspan="2"><h5>Custom Fields</h5></td>
        </tr>
        {{-- Custom Fields --}}
        @foreach($user->fields as $field)
          <tr>
            <td>{{ $field->field->name }}</td>
            <td>
              @if(in_array($field->name, ['IVAO', 'IVAO ID']))
                <a href='https://www.ivao.aero/Member.aspx?ID={{ $field->value }}' target='_blank'>{{ $field->value }}</a>
              @elseif(in_array($field->name, ['VATSIM', 'VATSIM CID', 'VATSIM ID']))
                <a href='https://stats.vatsim.net/search_id.php?id={{ $field->value }}' target='_blank'>{{ $field->value }}</a>
              @else
                {{ $field->value }}
              @endif
            </td>
          </tr>
        @endforeach
      </table>
    @endif
  </div>
  <div class="form-group col-sm-6">
    <table class="table table-hover">
      <tr>
        <td colspan="2"><h5>User Details</h5></td>
      </tr>
      <tr>
        <td>Total Flights</td>
        <td>{{ $user->flights }}</td>
      </tr>
      <tr>
        <td>Flight Time</td>
        <td>@minutestotime($user->flight_time)</td>
      </tr>
      <tr>
        <td>Registered On</td>
        <td>{{ show_datetime($user->created_at) }}</td>
      </tr>
      <tr>
        <td>E-Mail Verified On</td>
        <td>
          @if(filled($user->email_verified_at))
            {{ show_datetime($user->email_verified_at) }}
          @else
            <span class="btn btn-sm btn-danger mx-1 my-0 p-1">USER E-MAIL NOT VERIFIED !!!</span>
          @endif
        </td>
      </tr>
      <tr>
        <td>Last Login</td>
        <td>
          @if(filled($user->lastlogin_at))
            {{ show_datetime($user->lastlogin_at) }}
          @endif
        </td>
      </tr>
      <tr>
        <td>IP Address</td>
        <td>{{ $user->last_ip ?? '-' }}</td>
      </tr>
      <tr>
        <td>@lang('toc.title')</td>
        <td>{{ $user->toc_accepted ? __('common.yes') : __('common.no') }}</td>
      </tr>
      <tr>
        <td>@lang('profile.opt-in')</td>
        <td>{{ $user->opt_in ? __('common.yes') : __('common.no') }}</td>
      </tr>
    </table>
  </div>
</div>

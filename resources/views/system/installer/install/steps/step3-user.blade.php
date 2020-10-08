@extends('system.installer.app')
@section('title', 'User Setup')

@section('content')
  <div class="row">
    <div class="col-md-12">
      <div style="align-content: center;">
        {{ Form::open(['route' => 'installer.usersetup', 'method' => 'POST']) }}
        <table class="table" width="25%">

          <tr>
            <td colspan="2" style="text-align: right">
              <a href="{{ route('importer.index') }}">Importing from a legacy install?</a>
            </td>
          </tr>

          <tr>
            <td colspan="2"><h4>Airline Information</h4></td>
          </tr>

          <tr>
            <td><p>Airline ICAO</p></td>
            <td>
              <div class="form-group">
                {{ Form::input('text', 'airline_icao', null, ['class' => 'form-control']) }}
                @include('system.installer.flash/check_error', ['field' => 'airline_icao'])
              </div>
            </td>
          </tr>

          <tr>
            <td><p>Airline Name</p></td>
            <td>
              <div class="form-group">
                {{ Form::input('text', 'airline_name', null, ['class' => 'form-control']) }}
                @include('system.installer.flash/check_error', ['field' => 'airline_name'])
              </div>
            </td>
          </tr>

          <tr>
            <td><p>Airline Country</p></td>
            <td>
              <div class="form-group">
                {{ Form::select('airline_country', $countries, null, ['class' => 'form-control select2' ]) }}
                @include('system.installer.flash/check_error', ['field' => 'airline_country'])
              </div>
            </td>
          </tr>

          <tr>
            <td colspan="2"><h4>First User</h4></td>
          </tr>

          <tr>
            <td><p>Name</p></td>
            <td>
              <div class="form-group">
                {{ Form::input('text', 'name', null, ['class' => 'form-control']) }}
                @include('system.installer.flash/check_error', ['field' => 'name'])
              </div>
            </td>
          </tr>

          <tr>
            <td><p>Email</p></td>
            <td>
              <div class="form-group">
                {{ Form::input('text', 'email', null, ['class' => 'form-control']) }}
                @include('system.installer.flash/check_error', ['field' => 'email'])
              </div>
            </td>
          </tr>

          <tr>
            <td><p>Password</p></td>
            <td>
              {{ Form::password('password', ['class' => 'form-control']) }}
              @include('system.installer.flash/check_error', ['field' => 'password'])
            </td>
          </tr>

          <tr>
            <td width="40%"><p>Password Confirm</p></td>
            <td>
              {{ Form::password('password_confirmation', ['class' => 'form-control']) }}
              @include('system.installer.flash/check_error', ['field' => 'password_confirmation'])
            </td>
          </tr>

          <tr>
            <td colspan="2"><h4>Options</h4></td>
          </tr>

          <tr>
            <td><p>Analytics</p></td>
            <td>
              <div class="form-group">
                {{ Form::hidden('telemetry', 0) }}
                {{ Form::checkbox('telemetry', 1, true, ['class' => 'form-control']) }}
                <br/>
                <p>
                  Allows collection of analytics. They won't identify you, and helps us to track
                  the PHP and database versions that are used, and help to figure out problems
                  and slowdowns when vaCentral integration is enabled.
                </p>
              </div>
            </td>
          </tr>
        </table>
        <div id="dbtest"></div>
        <p style="text-align: right">
          {{ Form::submit('Complete Setup >>', ['class' => 'btn btn-success']) }}
        </p>
        {{ Form::close() }}
      </div>
    </div>
  </div>
@endsection

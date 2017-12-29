@extends('installer::app')
@section('title', 'User Setup')

@section('content')
    <div class="row"><div class="col-md-12">
<div style="align-content: center;">
    {!! Form::open(['route' => 'installer.usersetup', 'method' => 'POST']) !!}
    <table class="table" width="25%">

        <tr>
            <td colspan="2"><h4>Airline Information</h4></td>
        </tr>

        <tr>
            <td><p>Airline ICAO</p></td>
            <td>
                <div class="form-group">
                    {!! Form::input('text', 'airline_icao', null, ['class' => 'form-control']) !!}
                    @include('installer::flash/check_error', ['field' => 'airline_icao'])
                </div>
            </td>
        </tr>

        <tr>
            <td><p>Airline Name</p></td>
            <td>
                <div class="form-group">
                    {!! Form::input('text', 'airline_name', null, ['class' => 'form-control']) !!}
                    @include('installer::flash/check_error', ['field' => 'airline_name'])
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="2"><h4>First User</h4></td>
        </tr>

        <tr>
            <td><p>Admin Email</p></td>
            <td>
                <div class="form-group">
                    {!! Form::input('text', 'email', null, ['class' => 'form-control']) !!}
                    @include('installer::flash/check_error', ['field' => 'email'])
                </div>
            </td>
        </tr>

        <tr>
            <td><p>Password</p></td>
            <td>
                {!! Form::password('password', ['class' => 'form-control']) !!}
                @include('installer::flash/check_error', ['field' => 'password'])
            </td>
        </tr>

        <tr>
            <td><p>Password Confirm</p></td>
            <td>
                {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                @include('installer::flash/check_error', ['field' => 'password_confirmation'])
            </td>
        </tr>

    </table>
    <div id="dbtest"></div>
    <p style="text-align: right">
        {!! Form::submit('Complete Setup >>', ['class' => 'btn btn-success']) !!}
    </p>
    {!! Form::close() !!}
</div>
        </div>
    </div>
@endsection

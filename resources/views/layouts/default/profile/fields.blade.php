<div class="row">
    <div class="col-md-12">
        <table class="table">
            <tr>
                <td>Name</td>
                <td>
                    <div class="input-group form-group-no-border{{ $errors->has('name') ? ' has-danger' : '' }}">
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>
                    @if ($errors->has('name'))
                        <p class="text-danger">{{ $errors->first('name') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>Email</td>
                <td>
                    <div class="input-group form-group-no-border{{ $errors->has('email') ? ' has-danger' : '' }}">
                        {!! Form::text('email', null, ['class' => 'form-control']) !!}
                    </div>
                    @if ($errors->has('email'))
                        <p class="text-danger">{{ $errors->first('email') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>Airline</td>
                <td>
                    <div class="input-group form-group-no-border{{ $errors->has('airline') ? ' has-danger' : '' }}">
                        {!! Form::select('airline_id', $airlines, null , ['class' => 'form-control select2']) !!}
                    </div>
                    @if ($errors->has('airline_id'))
                        <p class="text-danger">{{ $errors->first('airline_id') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>Timezone</td>
                <td>
                    <div class="input-group form-group-no-border{{ $errors->has('timezone') ? ' has-danger' : '' }}">
                        {!! Form::select('timezone', $timezones, null, ['class' => 'form-control select2' ]); !!}
                    </div>
                    @if ($errors->has('timezone'))
                        <p class="text-danger">{{ $errors->first('timezone') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>Change Password</td>
                <td>
                    <div class="input-group form-group-no-border{{ $errors->has('password') ? ' has-danger' : '' }}">
                        {!! Form::password('password', ['class' => 'form-control']) !!}
                    </div>
                    @if ($errors->has('password'))
                        <p class="text-danger">{{ $errors->first('password') }}</p>
                    @endif

                    <p>Confirm Password:</p>
                    <div class="input-group form-group-no-border{{ $errors->has('password_confirmation') ? ' has-danger' : '' }}">
                        {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                    </div>
                    @if ($errors->has('password_confirmation'))
                        <p class="text-danger">{{ $errors->first('password_confirmation') }}</p>
                    @endif
                </td>
            </tr>

        </table>

        <div style="width: 100%; text-align: right; padding-top: 20px;">
            {!! Form::submit('Update Profile', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
</div>

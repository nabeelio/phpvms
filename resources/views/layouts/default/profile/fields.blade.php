<div class="row">
    <div class="col-md-12">
        <table class="table">
            <tr>
                <td>{{ __('Name') }}</td>
                <td>
                    <div class="input-group form-group-no-border{{ $errors->has('name') ? ' has-danger' : '' }}">
                        {{ Form::text('name', null, ['class' => 'form-control']) }}
                    </div>
                    @if ($errors->has('name'))
                        <p class="text-danger">{{ $errors->first('name') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>{{ __('Email') }}</td>
                <td>
                    <div class="input-group form-group-no-border{{ $errors->has('email') ? ' has-danger' : '' }}">
                        {{ Form::text('email', null, ['class' => 'form-control']) }}
                    </div>
                    @if ($errors->has('email'))
                        <p class="text-danger">{{ $errors->first('email') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>{{ __('Airline') }}</td>
                <td>
                    <div class="input-group form-group-no-border{{ $errors->has('airline') ? ' has-danger' : '' }}">
                        {{ Form::select('airline_id', $airlines, null , ['class' => 'form-control select2']) }}
                    </div>
                    @if ($errors->has('airline_id'))
                        <p class="text-danger">{{ $errors->first('airline_id') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>{{ __('Home Airport') }}</td>
                <td>
                    <div class="input-group form-group-no-border{{ $errors->has('home_airport_id') ? ' has-danger' : '' }}">
                        {{ Form::select('home_airport_id', $airports, null , ['class' => 'form-control select2']) }}
                    </div>
                    @if ($errors->has('home_airport_id'))
                        <p class="text-danger">{{ $errors->first('home_airport_id') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>{{ __('Country') }}</td>
                <td>
                    <div class="input-group form-group-no-border{{ $errors->has('country') ? ' has-danger' : '' }}">
                        {{ Form::select('country', $countries, null, ['class' => 'form-control select2' ]) }}
                    </div>
                    @if ($errors->has('country'))
                        <p class="text-danger">{{ $errors->first('country') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>{{ __('Timezone') }}</td>
                <td>
                    <div class="input-group form-group-no-border{{ $errors->has('timezone') ? ' has-danger' : '' }}">
                        {{ Form::select('timezone', $timezones, null, ['class' => 'form-control select2' ]) }}
                    </div>
                    @if ($errors->has('timezone'))
                        <p class="text-danger">{{ $errors->first('timezone') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>{{ __('Change Password') }}</td>
                <td>
                    <div class="input-group form-group-no-border{{ $errors->has('password') ? ' has-danger' : '' }}">
                        {{ Form::password('password', ['class' => 'form-control']) }}
                    </div>
                    @if ($errors->has('password'))
                        <p class="text-danger">{{ $errors->first('password') }}</p>
                    @endif

                    <p>{{ __('Confirm Password') }}:</p>
                    <div class="input-group form-group-no-border{{ $errors->has('password_confirmation') ? ' has-danger' : '' }}">
                        {{ Form::password('password_confirmation', ['class' => 'form-control']) }}
                    </div>
                    @if ($errors->has('password_confirmation'))
                        <p class="text-danger">{{ $errors->first('password_confirmation') }}</p>
                    @endif
                </td>
            </tr>
            <td>{{ __('Avatar') }}</td>
            <td>
                <div class="input-group form-group-no-border{{ $errors->has('avatar') ? ' has-danger' : '' }}">
                    {{ Form::file('avatar', null) }}
                </div>
            <p class="small">This avatar will be resized to {{ config('phpvms.avatar.width'). ' x '. config('phpvms.avatar.height') }}</p>
                @if ($errors->has('avatar'))
                    <p class="text-danger">{{ $errors->first('avatar') }}</p>
                @endif
            </td>

        </table>

        <div style="width: 100%; text-align: right; padding-top: 20px;">
            {{ Form::submit(__('Update Profile'), ['class' => 'btn btn-primary']) }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table">
            <tr>
                <td>@lang('frontend.global.name')</td>
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
                <td>@lang('frontend.global.email')</td>
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
                <td>@lang('frontend.global.airline')</td>
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
                <td>@lang('frontend.global.homeairport')</td>
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
                <td>@lang('frontend.global.country')</td>
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
                <td>@lang('frontend.global.timezone')</td>
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
                <td>@lang('frontend.profile.changepassword')</td>
                <td>
                    <p>@lang('frontend.profile.newpassword'):</p>
                    <div class="input-group form-group-no-border{{ $errors->has('password') ? ' has-danger' : '' }}">
                        {{ Form::password('password', ['class' => 'form-control']) }}
                    </div>
                    @if ($errors->has('password'))
                        <p class="text-danger">{{ $errors->first('password') }}</p>
                    @endif

                    <p>@lang('frontend.global.confirmpassword'):</p>
                    <div class="input-group form-group-no-border{{ $errors->has('password_confirmation') ? ' has-danger' : '' }}">
                        {{ Form::password('password_confirmation', ['class' => 'form-control']) }}
                    </div>
                    @if ($errors->has('password_confirmation'))
                        <p class="text-danger">{{ $errors->first('password_confirmation') }}</p>
                    @endif
                </td>
            </tr>
            <td>@lang('frontend.profile.avatar')</td>
            <td>
                <div class="input-group form-group-no-border{{ $errors->has('avatar') ? ' has-danger' : '' }}">
                    {{ Form::file('avatar', null) }}
                </div>
            <p class="small">@lang('frontend.profile.avatarresize', ['width' => config('phpvms.avatar.width'), 'height' => config('phpvms.avatar.height')])</p>
                @if ($errors->has('avatar'))
                    <p class="text-danger">{{ $errors->first('avatar') }}</p>
                @endif
            </td>

        </table>

        <div style="width: 100%; text-align: right; padding-top: 20px;">
            {{ Form::submit(trans('frontend.profile.updateprofile'), ['class' => 'btn btn-primary']) }}
        </div>
    </div>
</div>

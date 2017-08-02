<div class="section-story-overview">
    <div class="row">
        <div class="col-md-9 push-md-1">
            <p class="category">Update</p>
            {!! Form::model($user, ['url' => url('/profile'), 'method' => 'post']) !!}
            <div class="card">
                <div class="card-block">
                    <div class="input-group form-group-no-border{{ $errors->has('email') ? ' has-error' : '' }} input-lg">
                        <span class="input-group-addon">
                            <i class="fa fa-envelope-open-o" aria-hidden="true"></i>
                        </span>
                        {!! Form::email('email', null, [
                                'class' => 'form-control',
                                'placeholder' => 'Email',
                            ]) !!}
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

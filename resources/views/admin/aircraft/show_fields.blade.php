<!-- Icao Field -->
<div class="form-group col-sm-6">
    <div class="box box-solid">
        <div class="box-header with-border">
            {{--<i class="fa fa-text-width"></i>--}}
            <h3 class="box-title">{!! Form::label('icao', 'ICAO') !!}</h3>
        </div>
        <div class="box-body">
            <p class="lead">{!! $aircraft->icao !!}
            @if($aircraft->subfleet)
                /{!! $aircraft->subfleet->name !!}</p>
            @endif
        </div>
    </div>
</div>

<!-- Name Field -->
<div class="form-group col-sm-6">
    <div class="box box-solid">
        <div class="box-header with-border">
            {{--<i class="fa fa-text-width"></i>--}}
            <h3 class="box-title">{!! Form::label('name', 'Name') !!}</h3>
        </div>
        <div class="box-body"><p class="lead">{!! $aircraft->name !!}</p></div>
    </div>
</div>

<!-- Registration Field -->
<div class="form-group col-sm-6">
    <div class="box box-solid">
        <div class="box-header with-border">
            {{--<i class="fa fa-text-width"></i>--}}
            <h3 class="box-title">{!! Form::label('registration', 'Registration') !!}/
                {!! Form::label('tail_number', 'Tail Number') !!}</h3>
        </div>
        <div class="box-body"><p class="lead">{!! $aircraft->registration !!}/{!! $aircraft->tail_number !!}</p></div>
    </div>
</div>

<!-- Active Field -->
<div class="form-group col-sm-6">
    <div class="box box-solid">
        <div class="box-header with-border">
            {{--<i class="fa fa-text-width"></i>--}}
            <h3 class="box-title">{!! Form::label('active', 'Active:') !!}</h3>
        </div>
        <div class="box-body"><p class="lead">@if ($aircraft->active == '1') yes @else no @endif</p></div>
    </div>
</div>

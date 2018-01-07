<div id="flight_fares_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="header">
        <h3>fares</h3>
        <p class="category">
            <i class="icon fa fa-info">&nbsp;&nbsp;</i>
            Fares assigned to the current flight. These can be overridden,
            otherwise, the values used come from the subfleet of the aircraft
            that the flight is filed with. Only assign the fares you want to
            override.
        </p>
    </div>

    <table id="flight_fares"
           class="table table-hover"
           role="grid" aria-describedby="aircraft_fares_info">
        <thead>
        <tr role="row">
            <th>name</th>
            <th style="text-align: center;">code</th>
            <th>capacity</th>
            <th>price</th>
            <th>cost</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($flight->fares as $atf)
            <tr>
                <td class="sorting_1">{!! $atf->name !!}</td>
                <td style="text-align: center;">{!! $atf->code !!}</td>
                <td>
                    <a href="#" data-pk="{!! $atf->id !!}" data-name="capacity">{!! $atf->pivot->capacity !!}</a>
                </td>
                <td>
                    <a href="#" data-pk="{!! $atf->id !!}" data-name="price">{!! $atf->pivot->price !!}</a>
                </td>
                <td>
                    <a href="#" data-pk="{!! $atf->id !!}" data-name="cost">{!! $atf->pivot->cost !!}</a>
                </td>
                <td style="text-align: right; width:3%;">
                    {!! Form::open(['url' => '/admin/flights/'.$flight->id.'/fares',
                                    'method' => 'delete',
                                    'class' => 'pjax_fares_form'
                                    ])
                    !!}
                    {!! Form::hidden('fare_id', $atf->id) !!}
                    {!! Form::button('<i class="fa fa-times"></i>',
                                     ['type' => 'submit',
                                      'class' => 'btn btn-sm btn-danger btn-icon']) !!}
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr />
    <div class="row">
        <div class="col-xs-12">
            <div class="text-right">
                {!! Form::open(['url' => '/admin/flights/'.$flight->id.'/fares',
                                'method' => 'post',
                                'class' => 'pjax_fares_form form-inline'
                                ])
                !!}
                {!! Form::select('fare_id', $avail_fares, null, [
                        'placeholder' => 'Select Fare',
                        'class' => 'ac-fare-dropdown form-control input-lg select2',

                    ])
                !!}
                {!! Form::button('<i class="glyphicon glyphicon-plus"></i> add',
                                 ['type' => 'submit',
                                  'class' => 'btn btn-success btn-s']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

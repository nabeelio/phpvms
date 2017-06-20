<div id="flight_aircraft_wrapper" >
<table class="table table-responsive" id="aircrafts-table">
    <thead>
    <th>ICAO</th>
    <th>Name</th>
    <th>Registration</th>
    <th style="text-align: center;">Actions</th>
    </thead>
    <tbody>
    @foreach($flight->aircraft as $ac)
        <tr>
            <td>{!! $ac->icao !!}</td>
            <td>{!! $ac->name !!}</td>
            <td>{!! $ac->registration !!}</td>
            <td style="width: 10%; text-align: center;" class="form-inline">
                {!! Form::open(['url' => '/admin/flights/'.$flight->id.'/aircraft', 'method' => 'delete', 'class' => 'flight_ac_frm']) !!}
                {!! Form::hidden('aircraft_id', $flight->id) !!}
                <div class='btn-group'>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                                     ['type' => 'submit',
                                      'class' => 'btn btn-danger btn-xs'])
                      !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<hr/>
<div class="row">
    <div class="col-xs-12">
        <div class="input-group input-group-lg pull-right">
            {!! Form::open(['url' => '/admin/flights/'.$flight->id.'/aircraft',
                            'method' => 'post',
                            'class' => 'flight_ac_frm form-inline'
                            ])
            !!}
            {!! Form::select('aircraft_id', $avail_aircraft, null, [
                    'placeholder' => 'Select Aircraft',
                    'class' => 'ac-flight-dropdown form-control input-lg',
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

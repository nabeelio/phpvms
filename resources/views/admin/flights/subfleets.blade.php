<div id="subfleet_flight_wrapper">
<table class="table table-responsive" id="aircrafts-table">
    <thead>
    <th>Type</th>
    <th>Name</th>
    <th style="text-align: center;">Actions</th>
    </thead>
    <tbody>
    @foreach($flight->subfleets as $sf)
        <tr>
            <td>{!! $sf->type !!}</td>
            <td>{!! $sf->name !!}</td>
            <td style="width: 10%; text-align: center;" class="form-inline">
                {!! Form::open(['url' => '/admin/flights/'.$flight->id.'/subfleets', 'method' => 'delete', 'class' => 'flight_subfleet']) !!}
                {!! Form::hidden('subfleet_id', $sf->id) !!}
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
            {!! Form::open(['url' => '/admin/flights/'.$flight->id.'/subfleets',
                            'method' => 'post',
                            'class' => 'flight_subfleet form-inline'
                            ])
            !!}
            {!! Form::select('subfleet_id', $avail_subfleets, null, [
                    'placeholder' => 'Select Subfleet',
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

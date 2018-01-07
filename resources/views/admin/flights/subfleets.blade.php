<div id="subfleet_flight_wrapper">
    <h3>assigned subfleets</h3>
    <br />

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
                    {!! Form::open(['url' => '/admin/flights/'.$flight->id.'/subfleets',
                                    'method' => 'delete',
                                    'class' => 'pjax_subfleet_form']) !!}
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
            <div class="text-right">
                {!! Form::open([
                        'url' => '/admin/flights/'.$flight->id.'/subfleets',
                        'method' => 'post',
                        'class' => 'pjax_form form-inline pjax_subfleet_form'
                    ])
                !!}
                {!! Form::select('subfleet_id', $avail_subfleets, null, [
                        'placeholder' => 'Select Subfleet',
                        'class' => 'select2_dropdown form-control input-lg',
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

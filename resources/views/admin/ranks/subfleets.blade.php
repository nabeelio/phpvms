<div id="rank_subfleet_wrapper" class="dataTables_wrapper form-inline dt-bootstrap col-lg-12">
    <table class="table table-responsive" id="subfleets-table">
        <thead>
        <th>Airline</th>
        <th>Name</th>
        <th>Type</th>
        <th style="text-align: center;">Actions</th>
        </thead>
        <tbody>
        @foreach($rank->subfleets as $sf)
            <tr>
                <td>{!! $sf->airline->name !!}</td>
                <td>{!! $sf->name !!}</td>
                <td>{!! $sf->type !!}</td>
                <td style="width: 10%; text-align: center;" class="form-inline">
                    {!! Form::open(['url' => '/admin/ranks/'.$rank->id.'/subfleets', 'method' => 'delete', 'class' => 'pjax_form']) !!}
                    {!! Form::hidden('subfleet_id', $sf->id) !!}
                    <div class='btn-group'>
                        {!! Form::button('<i class="fa fa-times"></i>',
                                         ['type' => 'submit',
                                          'class' => 'btn btn-sm btn-danger btn-icon'])
                          !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr />
    <div class="row">
        <div class="col-lg-12">
            <div class="input-group input-group-lg pull-right">
                {!! Form::open(['url' => url('/admin/ranks/'.$rank->id.'/subfleets'),
                                'method' => 'post',
                                'class' => 'pjax_form form-inline'
                                ])
                !!}
                {!! Form::select('subfleet_id', $avail_subfleets, null, [
                        'placeholder' => 'Select Subfleet',
                        'class' => 'select2 form-control input-lg'])
                !!}
                {!! Form::button('<i class="fa fa-plus"></i> Add',
                                 ['type' => 'submit',
                                  'class' => 'btn btn-success btn-small']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

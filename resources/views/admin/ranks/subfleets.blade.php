<div id="rank_subfleet_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
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
    <hr />
    <div class="row">
        <div class="col-xs-12">
            <div class="input-group input-group-lg pull-right">
                {!! Form::open(['url' => '/admin/ranks/'.$rank->id.'/subfleets',
                                'method' => 'post',
                                'class' => 'pjax_form form-inline'
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
<script>
$(document).ready(function () {
    $(".select2_dropdown").select2();
});
</script>

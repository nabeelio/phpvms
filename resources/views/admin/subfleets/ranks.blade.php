<div id="subfleet_ranks_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="header">
        <h3>ranks</h3>
        <p class="category">
            <i class="icon fa fa-info">&nbsp;&nbsp;</i>
            These ranks are allowed to fly aircraft in this subfleet
        </p>
    </div>
    <br />
    <table id="subfleet_ranks" class="table table-hover dataTable">
        <thead>
        <tr>
            <th>name</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($subfleet->ranks as $rank)
            <tr>
                <td class="sorting_1">{!! $rank->name !!}</td>
                <td style="text-align: right; width:3%;">
                    {!! Form::open(['url' => '/admin/subfleets/'.$subfleet->id.'/ranks',
                                    'method' => 'delete',
                                    'class' => 'modify_rank'])
                    !!}
                    {!! Form::hidden('rank_id', $rank->id) !!}
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
            {!! Form::open(['url' => '/admin/subfleets/'.$subfleet->id.'/ranks',
                            'method' => 'post',
                            'class' => 'modify_rank form-inline'])
            !!}
            {!! Form::select('rank_id', $avail_ranks, null, [
                    'placeholder' => 'Select Rank',
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
{{--</div></div>--}}

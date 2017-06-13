<div id="aircraft_fares_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
    <table id="aircraft_fares"
           class="table table-bordered table-hover dataTable"
           role="grid" aria-describedby="aircraft_fares_info">
        <thead>
        <tr role="row">
            <th class="sorting" tabindex="0" aria-controls="aircraft_fares"
                rowspan="1" colspan="1"
                aria-label="name: activate to sort column ascending">
                name
            </th>
            <th class="sorting_asc" tabindex="0"
                aria-controls="aircraft_fares" rowspan="1" colspan="1"
                aria-sort="ascending"
                aria-label="code: activate to sort column descending">
                code
            </th>
            <th class="sorting" tabindex="0" aria-controls="aircraft_fares"
                rowspan="1" colspan="1"
                aria-label="capacity: activate to sort column ascending">
                capacity
            </th>
            <th class="sorting" tabindex="0" aria-controls="aircraft_fares"
                rowspan="1" colspan="1"
                aria-label="price: activate to sort column ascending">
                price
            </th>
            <th class="sorting" tabindex="0" aria-controls="aircraft_fares"
                rowspan="1" colspan="1"
                aria-label="cost: activate to sort column ascending">
                cost
            </th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($attached_fares as $atf)
            <tr role="row" class="@if ($loop->iteration%2) even @else odd @endif">
                <td class="sorting_1">{!! $atf->name !!}</td>
                <td>{!! $atf->code !!}</td>
                <td>{!! $atf->capacity !!}</td>
                <td>{!! $atf->price !!}</td>
                <td>{!! $atf->cost !!}</td>
                <td style="text-align: right; width:3%;">
                    <div class='btn-group'>
                        {!! Form::open(['url' => '/admin/aircraft/'.$aircraft->id.'/fares', 'method' => 'delete', 'class' => 'rm_fare']) !!}
                        {!! Form::hidden('fare_id', $atf->id) !!}
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                                         ['type' => 'submit',
                                          'class' => 'btn btn-danger btn-s']) !!}
                        {!! Form::close() !!}
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr />
    {!! $avail_fares !!}
</div>

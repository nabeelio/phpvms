<div class="row"> <div class="col-12">
<div id="flight_fields_wrapper">
    <table class="table table-responsive" id="flight-fields-table">
        <thead>
        <th>Name</th>
        <th>Value</th>
        <th style="text-align: center;">Actions</th>
        </thead>
        <tbody>
        @foreach($flight->fields as $field)
            <tr>
                <td>{!! $field->name !!}</td>
                <td>
                    <a class="inline" href="#" data-pk="{!! $field->id !!}" data-name="{!! $field->name !!}">{!! $field->value !!}</a>
                </td>
                <td style="width: 10%; text-align: center;" class="form-inline">
                    {!! Form::open(['url' => '/admin/flights/'.$flight->id.'/fields',
                                    'method' => 'delete',
                                    'class' => 'pjax_form flight_fields'
                                    ]) !!}
                    {!! Form::hidden('field_id', $field->id) !!}
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
    <div class="row pull-right">
        {!! Form::open([
                'url' => '/admin/flights/'.$flight->id.'/fields',
                'method' => 'post',
                'class' => 'pjax_form form-inline'
            ])
        !!}

        <div class="form-group col-xs-12 form-inline">

            {!! Form::label('name', 'Name:') !!}
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
            &nbsp;&nbsp;
            {!! Form::label('value', 'Value:') !!}
            {!! Form::text('value', null, ['class' => 'form-control']) !!}
            &nbsp;&nbsp;
            {!! Form::button('<i class="glyphicon glyphicon-plus"></i> add',
                             ['type' => 'submit',
                              'class' => 'btn btn-success btn-s']) !!}
        </div>
        {!! Form::close() !!}
    </div>
</div>
</div> </div>

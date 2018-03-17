<div id="flight_fields_wrapper">
    <h3>custom fields</h3><br />
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
                                    'class' => 'pjax_form pjax_flight_fields'
                                    ]) !!}
                    {!! Form::hidden('field_id', $field->id) !!}
                    <div class='btn-group'>
                        {!! Form::button('<i class="fa fa-times"></i>',
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
        <div class="col-md-12">
            <div class="text-right">
                {!! Form::open([
                    'url' => '/admin/flights/'.$flight->id.'/fields',
                    'method' => 'post',
                    'class' => 'pjax_form form-inline pjax_flight_fields'
                ])
                !!}

                {!! Form::label('name', 'Name:') !!}
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                &nbsp;&nbsp;
                {!! Form::label('value', 'Value:') !!}
                {!! Form::text('value', null, ['class' => 'form-control']) !!}
                &nbsp;&nbsp;
                {!! Form::button('<i class="glyphicon glyphicon-plus"></i> add',
                                 ['type' => 'submit',
                                  'class' => 'btn btn-success btn-s']) !!}
                <p class="text-danger">{{ $errors->first('name') }}</p>
                <p class="text-danger">{{ $errors->first('value') }}</p>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

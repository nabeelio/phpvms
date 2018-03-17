<div id="pirep_field_values_wrapper">
<table class="table table-responsive" id="flight-fields-table">
    <thead>
    <th>Name</th>
    <th>Value</th>
    <th>Source</th>
    <th style="text-align: right;">Actions</th>
    </thead>
    <tbody>
    @foreach($pirep->fields as $field)
        <tr>
            <td>{!! $field->name !!}</td>
            <td>
                <a class="inline" href="#" data-pk="{!! $field->id !!}" data-name="{!! $field->name !!}">{!! $field->value !!}</a>
            </td>
            <td>{!! PirepSource::label($field->source) !!}</td>
            <td style="width: 10%; text-align: right;" class="form-inline">
                {!! Form::open(['url' => '/admin/pireps/'.$pirep->id.'/fields',
                                'method' => 'delete',
                                'class' => 'pjax_form pirep_fields'
                                ]) !!}
                {!! Form::hidden('field_id', $field->id) !!}
                <div class='btn-group'>
                    {{--{!! Form::button('<i class="fa fa-times"></i>',
                                     ['type' => 'submit',
                                      'class' => 'btn btn-danger btn-xs'])
                      !!}--}}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>

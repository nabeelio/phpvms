<div id="flight_fields_wrapper">
  <h3>custom fields</h3><br/>
  <table class="table table-responsive" id="flight-fields-table">
    @if(count($flight->field_values))
      <thead>
      <th>Name</th>
      <th>Value</th>
      <th></th>
      </thead>
    @endif
    <tbody>
    @php
      #
      # A little nasty having logic like this in a template, but we need
      # to filter out the field values that have already been shown, since
      # they were values set because they had a FlightField parent
      #
      $shown = [];
    @endphp
    @foreach($flight_fields->concat($flight->field_values) as $field)
      @php
        if(in_array($field->name, $shown, true)) {
            continue;
        }

        $shown[] = $field->name;
        $val_field = $flight->field_values->where('name', $field->name)->first();
      @endphp
      <tr>
        <td>{{ $field->name }}</td>
        <td>
          <a class="inline" href="#" data-pk="{{ $val_field['id'] ?? '' }}" data-name="{{ $field->name }}">
            {{ $val_field['value'] ?? '' }}
          </a>
        </td>
        <td style="width: 10%; text-align: center;" class="form-inline">
          {{ Form::open(['url' => '/admin/flights/'.$flight->id.'/fields',
                          'method' => 'delete',
                          'class' => 'pjax_form pjax_flight_fields'
                          ]) }}
          {{ Form::hidden('field_id', $val_field['id'] ?? null) }}
          <div class='btn-group'>
            {{ Form::button('<i class="fa fa-times"></i>',
                 ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon',
                  'onclick' => "return confirm('Are you sure?')"]) }}
          </div>
          {{ Form::close() }}
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <hr/>
  <div class="row">
    <div class="col-md-12">
      <div class="text-right">
        {{ Form::open([
            'url' => '/admin/flights/'.$flight->id.'/fields',
            'method' => 'post',
            'class' => 'pjax_form form-inline pjax_flight_fields'
        ])
        }}

        {{ Form::label('name', 'Name:') }}
        {{ Form::text('name', null, ['class' => 'form-control']) }}
        &nbsp;&nbsp;
        {{ Form::label('value', 'Value:') }}
        {{ Form::text('value', null, ['class' => 'form-control']) }}
        &nbsp;&nbsp;
        {{ Form::button('<i class="glyphicon glyphicon-plus"></i> add',
                         ['type' => 'submit',
                          'class' => 'btn btn-success btn-s']) }}
        <p class="text-danger">{{ $errors->first('name') }}</p>
        <p class="text-danger">{{ $errors->first('value') }}</p>
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

<table class="table table-responsive table-hover" id="flight-fields-table">
  @if(count($pirep->fields))
    <thead>
    <th></th>
    <th>Value</th>
    <th>Source</th>
    </thead>
  @endif
  <tbody>
  @foreach($pirep->fields as $field)
    <tr>
      <td>
        {{ $field->name }}
        @if($field->required === true)
          <span class="text-danger">*</span>
        @endif
      </td>
      <td>
        <div class="form-group">
          @if(!$field->read_only)
            {{ Form::text($field->slug, $field->value, [
                'class' => 'form-control'
                ]) }}
          @else
            <p>{{ $field->value }}</p>
          @endif
        </div>
        <p class="text-danger">{{ $errors->first($field->slug) }}</p>
      </td>
      <td>
        {{ PirepSource::label($field->source) }}
      </td>
    </tr>
  @endforeach
  </tbody>
</table>

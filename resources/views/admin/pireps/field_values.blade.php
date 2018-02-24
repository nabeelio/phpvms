<table class="table table-responsive table-hover" id="flight-fields-table">
    <thead>
    <th></th>
    <th>Value</th>
    <th>Source</th>
    </thead>
    <tbody>
    @foreach($pirep->fields as $field)
        <tr>
            <td>
                {!! $field->name !!}
                @if($field->required === true)
                    <span class="text-danger">*</span>
                @endif
            </td>
            <td>
                <div class="form-group">
                    {!! Form::text($field->slug, null, [
                        'class' => 'form-control'
                        ]) !!}
                </div>
                <p class="text-danger">{{ $errors->first($field->slug) }}</p>
            </td>
            <td>
                {!! PirepSource::label($field->source) !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<tr>
  <td>
    {{ $field->field->name }}
    @if($field->field->required === true)
      <span class="text-danger">*</span>
    @endif
  </td>
  <td>
    <div class="input-group input-group-sm form-group">
      {{ Form::text($field->field->slug, $field->value, [
          'class' => 'form-control',
          ]) }}
    </div>
    <p class="text-danger">{{ $errors->first('field_'.$field->slug) }}</p>
  </td>
</tr>

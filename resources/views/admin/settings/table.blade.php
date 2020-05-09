{{ Form::model($grouped_settings, ['route' => ['admin.settings.update'], 'method' => 'post']) }}
@foreach($grouped_settings as $group => $settings)
  <div class="card border-blue-bottom">
    <div class="content table-responsive">
      <div class="row">
        <table class="table table-hover" id="flights-table">
          <thead>
          <th colspan="2">
            <h5>{{ $group }}</h5>
          </th>
          </thead>

          @foreach($settings as $setting)
            <tr>
              <td width="70%">
                <p>{{ $setting->name }}</p>
                <p class="description">
                  @component('admin.components.info')
                    {{$setting->description}}
                  @endcomponent
                </p></td>
              <td align="center">
                @if($setting->type === 'date')
                  {{ Form::input('text', $setting->id, $setting->value, ['class' => 'form-control', 'id' => 'datepicker']) }}
                @elseif($setting->type === 'boolean' || $setting->type === 'bool')
                  {{ Form::hidden($setting->id, 0)  }}
                  {{ Form::checkbox($setting->id, null, $setting->value) }}
                @elseif($setting->type === 'int')
                  {{ Form::number($setting->id, $setting->value, ['class'=>'form-control']) }}
                @elseif($setting->type === 'number')
                  {{ Form::number($setting->id, $setting->value, ['class'=>'form-control', 'step' => '0.01']) }}
                @elseif($setting->type === 'select')

                  @if($setting->id === 'general_theme')
                    {{ Form::select(
                          $setting->id,
                          list_to_assoc($themes),
                          $setting->value,
                          ['class' => 'select2', 'style' => 'width: 100%; text-align: left;']) }}
                  @elseif($setting->id === 'units_currency')
                    {{ Form::select(
                          $setting->id,
                          $currencies,
                          $setting->value,
                          ['class' => 'select2', 'style' => 'width: 100%; text-align: left;']) }}
                  @else
                    {{ Form::select(
                            $setting->id,
                             list_to_assoc(explode(',', $setting->options)),
                             $setting->value,
                             ['class' => 'select2', 'style' => 'width: 100%; text-align: left;']) }}
                  @endif
                @else
                  {{ Form::input('text', $setting->id, $setting->value, ['class' => 'form-control']) }}
                @endif

              </td>
            </tr>
          @endforeach
        </table>
      </div>

      {{--<div class="row">
        <div class="col-sm-12 text-right">
          {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
          <a href="{{ route('admin.subfleets.index') }}" class="btn btn-default">Cancel</a>
        </div>
      </div>--}}

    </div>
  </div>
@endforeach

<div class="card">
  <div class="content">
    <div class="row">
      <div class="col-sm-12 text-right">
        {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
        <a href="{{ route('admin.subfleets.index') }}" class="btn btn-default">Cancel</a>
      </div>
    </div>
  </div>
</div>

{{ Form::close() }}

<script>
  $(document).ready(function () {
    $('#datepicker').datetimepicker({
      format: "YYYY-MM-DD"
    });
  });
</script>

{!! Form::model($grouped_settings, ['route' => ['admin.settings.update'], 'method' => 'post']) !!}
@foreach($grouped_settings as $group => $settings)
    <div class="card">
        <div class="content table-responsive table-full-width">
            <table class="table table-hover" id="flights-table">
                <thead>
                    <th colspan="2">
                        <h5>{!! $group !!}</h5>
                    </th>
                </thead>

                @foreach($settings as $setting)
                    <tr>
                        <td>
                            <p>{!! $setting->name !!}</p>
                            <p class="description">{{$setting->description}}</p></td>
                        <td>
                            @if($setting->type === 'text')
                                {!! Form::input('text', $setting->id, $setting->value, ['class' => 'form-control']) !!}
                            @elseif($setting->type === 'boolean' || $setting->type === 'bool')
                                {!! Form::hidden($setting->id, 0)  !!}
                                {!! Form::checkbox($setting->id, null, $setting->value, ['']) !!}
                            @elseif($setting->type === 'int' || $setting->type === 'number')
                                {!! Form::number($setting->id, $setting->value, ['class'=>'form-control']) !!}
                            @elseif($setting->type === 'select')
                                {!! Form::select($setting->id,
                                                 explode(',', $setting->options),
                                                 $setting->value, ['class' => 'select2', 'style' => 'width: 100%']) !!}
                            @else
                                {!! Form::input('text', $setting->id, $setting->value, ['class' => 'form-control']) !!}
                            @endif

                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endforeach
    <div class="pull-right">
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('admin.subfleets.index') !!}" class="btn btn-default">Cancel</a>
    </div>
{!! Form::close() !!}

<table class="table table-responsive" id="settings-table">
<thead>
    <th>Name</th>
    <th>Value</th>
    <th>Description</th>
</thead>
<tbody>
@foreach($settings as $s)
    <tr>
        <td>{!! $s->key !!}</td>
        <td>{!! $s->value !!}</td>
        <td>
            @if(Setting::get($s->key.'_descrip'))
                {!! Setting::get($s->key.'_descrip') !!}
            @else
                &nbsp;
            @endif
        </td>
    </tr>
@endforeach
</tbody>
</table>

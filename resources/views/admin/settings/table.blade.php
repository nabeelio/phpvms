<table class="table table-responsive" id="settings-table">
<thead>
    <th>Name</th>
    <th>Value</th>
</thead>
<tbody>
@foreach($settings as $s)
    <tr>
        <td>{!! $s->key !!}</td>
        <td>{!! $s->value !!}</td>
    </tr>
@endforeach
</tbody>
</table>

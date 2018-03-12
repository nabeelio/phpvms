@if($aircraft)
    <h3 class="description">Fares</h3>
    <table class="table table-hover">
        <thead>
        <th></th>
        <th>Count</th>
        </thead>
        </thead>
        <tbody>
        @foreach($aircraft->subfleet->fares as $fare)
            <tr>
                <td style="text-align: right;">{{ $fare->name }} ({{ $fare->code }})</td>
                <td>
                    @if($read_only)
                        <p>{{ $pirep->{'fare_'.$fare->id} }}</p>
                        {{ Form::hidden('fare_'.$fare->id) }}
                    @else
                        <div class="input-group form-group">
                            {{ Form::number('fare_'.$fare->id, null, ['class' => 'form-control', 'min' => 0]) }}
                        </div>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

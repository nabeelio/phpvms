@if($aircraft)
  <table class="table table-hover table-responsive">
    <thead>
    <th>Fare</th>
    <th>Count</th>
    </thead>
    </thead>
    <tbody>
    @foreach($aircraft->subfleet->fares as $fare)
      <tr>
        <td>{{ $fare->name }} ({{ $fare->code }})</td>
        <td>
          <div class="form-group">
            @if(isset($pirep) && $pirep->read_only)
              <p>{{ $pirep->{'fare_'.$fare->id} }}</p>
              {{ Form::hidden('fare_'.$fare->id) }}
            @else
              {{ Form::number('fare_'.$fare->id, null, [
                  'class' => 'form-control',
                  'min' => 0,
                  'step' => '0.01',
                  ]) }}
            @endif
          </div>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
@endif

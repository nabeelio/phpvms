@if($pirep->fares)
  <table class="table table-hover table-responsive">
    <thead>
      <th>Fare</th>
      <th>Count</th>
      <th>Price</th>
      <th>Capacity</th>
    </thead>
    </thead>
    <tbody>
    @foreach($pirep->fares as $fare)
      <tr>
        <td>{{ $fare->name }} ({{ $fare->code }})</td>
        <td>
          <div class="form-group">
            @if(isset($pirep) && $pirep->read_only)
              <p>{{ $fare->count }}</p>
            @else
              {{ Form::number('fare_'.$fare->id.'_count', $fare->count, [
                  'class' => 'form-control',
                  'min' => 0,
                  'step' => '0.01',
                  ]) }}
            @endif
          </div>
        </td>
        <td>
          @if(isset($pirep) && $pirep->read_only)
              <p>{{ $fare->price }}</p>
            @else
              {{ Form::number('fare_'.$fare->id.'_price', $fare->price, [
                  'class' => 'form-control',
                  'min' => 0,
                  'step' => '0.01',
                  ]) }}
            @endif
        </td>
        <td>
          {{ $fare->capacity }}
        </td>

      </tr>
    @endforeach
    </tbody>
  </table>
@endif

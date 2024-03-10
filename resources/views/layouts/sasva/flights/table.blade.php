
<table>
  <thead>
    <tr>
      <th>Flight number</th>
      <th>Departure Airport</th>
      <th>Arrival Airport</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    @foreach($flights as $flight)
      <tr>
        <td>{{ $flight->ident }}</td>
        <td>
          <span>{{ optional($flight->dpt_airport)->name ?? $flight->dpt_airport_id }} ({{$flight->dpt_airport_id}})</span>
          <span>{{ $flight->dpt_time }}</span>
        </td>
        <td>
          <span>{{ optional($flight->arr_airport)->name ?? $flight->arr_airport_id }} ({{$flight->arr_airport_id}})</span>
          <span>{{ $flight->arr_time }}</span>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>


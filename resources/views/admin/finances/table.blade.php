@foreach($transaction_groups as $group)

  <h3>{{ $group['airline']->icao }} - {{ $group['airline']->name }}</h3>

  <table
    id="finances-table"
    style="width: 95%; margin: 0px auto;"
    class="table table-hover table-responsive">

    <thead>
    <th>Expenses</th>
    <th>Credit</th>
    <th>Debit</th>
    </thead>
    <tbody>
    @foreach($group['transactions'] as $ta)
      <tr>
        <td>
          {{ $ta->transaction_group }}
        </td>
        <td>
          @if($ta->sum_credits)
            {{ money($ta->sum_credits, $ta->currency) }}
          @endif
        </td>
        <td>
          @if($ta->sum_debits)
            <i>{{ money($ta->sum_debits, $ta->currency) }}</i>
          @endif
        </td>
      </tr>
    @endforeach

    {{-- show sums --}}
    <tr>
      <td></td>
      <td>
        {{ $group['credits'] }}
      </td>
      <td>
        <i>{{ $group['debits'] }}</i>
      </td>
    </tr>

    {{-- final total --}}
    <tr style="border-top: 3px; border-top-style: double;">
      <td></td>
      <td align="right">
        <b>Total</b>
      </td>
      <td>
        {{ $group['credits']->subtract($group['debits']) }}
      </td>
    </tr>

    </tbody>
  </table>

  @if(!$loop->last)
    <hr>
  @endif
@endforeach

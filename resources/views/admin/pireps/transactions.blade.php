<div class="col-12">
  @if(count($journal['transactions']) > 0)
    <table class="table table-hover" id="users-table">
      <tbody>
      @foreach($journal['transactions'] as $entry)
        <tr>
          <td>{{ $entry->memo }}</td>
          <td>
            @if($entry->credit)
              {{ money($entry->credit, setting('units.currency')) }}
            @endif
          </td>
          <td>
            @if($entry->debit)
              {{ money($entry->debit, setting('units.currency')) }}
            @endif
          </td>
        </tr>
      @endforeach

      {{-- show sums --}}
      <tr>
        <td></td>
        <td>
          {{ $journal['credits'] }}
        </td>
        <td>
          <i>{{ $journal['debits'] }}</i>
        </td>
      </tr>

      {{-- final total --}}
      <tr style="border-top: 3px; border-top-style: double;">
        <td></td>
        <td align="right">
          <b>Total</b>
        </td>
        <td>
          {{ $journal['credits']->subtract($journal['debits']) }}
        </td>
      </tr>
      </tbody>
    </table>
</div>
@endif

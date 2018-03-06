
@foreach($transaction_groups as $group)

    <h4>{!! $group['airline']->icao !!} - {!! $group['airline']->name !!}</h4>

    <table class="table table-hover table-responsive" id="finances-table">
        <thead>
        <th>Expenses</th>
        <th>Credit</th>
        <th>Debit</th>
        </thead>
        <tbody>
        @foreach($group['transactions'] as $ta)
            <tr>
                <td>
                    {!! $ta->transaction_group !!}
                </td>
                <td>
                    @if($ta->sum_credits)
                        {!! money($ta->sum_credits, $ta->currency) !!}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($ta->sum_debits)
                        <i>{!! money($ta->sum_debits, $ta->currency) !!}</i>
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach

        {{-- show sums --}}
        <tr>
            <td></td>
            <td>
                {!! $group['credits'] !!}
            </td>
            <td>
                ({!! $group['debits'] !!})
            </td>
        </tr>

        {{-- final total --}}
        <tr style="border-top: 3px; border-top-style: double;">
            <td></td>
            <td><b>Total</b></td>
            <td>
                {!! $group['credits']->subtract($group['debits']) !!}
            </td>
        </tr>

        </tbody>
    </table>

    @if(!$loop->last)
        <hr>
    @endif
@endforeach

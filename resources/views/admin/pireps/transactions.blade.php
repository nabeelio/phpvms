@if(count($journal['transactions']) > 0)
<div class="col-12">
    <table class="table table-hover" id="users-table">
        <tbody>
        @foreach($journal['transactions'] as $entry)
            <tr>
                <td>{!! $entry->memo !!}</td>
                <td>
                    @if($entry->credit)
                        {!! money($entry->credit, config('phpvms.currency')) !!}
                    @endif
                </td>
                <td>
                    @if($entry->debit)
                        {!! money($entry->debit, config('phpvms.currency')) !!}
                    @endif
                </td>
            </tr>
        @endforeach

        {{-- show sums --}}
        <tr>
            <td></td>
            <td>
                {!! $journal['credits'] !!}
            </td>
            <td>
                ({!! $journal['debits'] !!})
            </td>
        </tr>

        {{-- final total --}}
        <tr>
            <td></td>
            <td><b>Total</b></td>
            <td>
                {!! $journal['credits']->subtract($journal['debits']) !!}
            </td>
        </tr>
        </tbody>
    </table>
</div>
@endif

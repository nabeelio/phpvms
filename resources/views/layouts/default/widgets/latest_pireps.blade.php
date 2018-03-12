<div class="card">
    <div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
        Recent Reports
    </div>
    <div class="card-block">
        <!-- Tab panes -->
        <div class="tab-content">
            <table>
                @foreach($pireps as $p)
                    <tr>
                        <td style="padding-right: 10px;">
                            <span class="title">{{ $p->airline->code }}</span>
                        </td>
                        <td>
                            {{ $p->dpt_airport_id }}-
                            {{ $p->arr_airport_id }}&nbsp;
                            {{ $p->aircraft->name }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>

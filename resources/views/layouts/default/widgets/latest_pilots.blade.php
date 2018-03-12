<div class="card">
    <div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
        Newest Pilots
    </div>
    <div class="card-block">
        <!-- Tab panes -->
        <div class="tab-content">
            <table>
                @foreach($users as $u)
                    <tr>
                        <td style="padding-right: 10px;">
                            <span class="title">{{ $u->pilot_id }}</span>
                        </td>
                        <td>{{ $u->name }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>

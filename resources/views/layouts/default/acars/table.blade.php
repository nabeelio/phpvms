<div id="flights_table" class="row">
    <div class="col-md-12">
        <h3 class="description">flights</h3>
        <table class="table">
            @foreach($pireps as $pirep)
                <tr>
                    <td>{!! $pirep->ident !!}</td>
                    <td>{!! $pirep->dpt_airport_id !!}</td>
                    <td>{!! $pirep->arr_airport_id !!}</td>
                    <td>
                        {!! PirepStatus::label($pirep->status); !!}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>

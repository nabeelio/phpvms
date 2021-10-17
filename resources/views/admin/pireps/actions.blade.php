<table class="table" style="border-style: hidden; margin-bottom: 0px;">
  <tr style="border-style: hidden;">
    @if ($pirep->state === PirepState::PENDING || $pirep->state === PirepState::REJECTED)
      <td>
        {{ Form::open(['url' => route('admin.pirep.status', [$pirep->id]),
                        'method' => 'post',
                        'name' => 'accept_'.$pirep->id,
                        'id' => $pirep->id.'_accept',
                        'pirep_id' => $pirep->id,
                        'new_status' => PirepState::ACCEPTED,
                        'class' => $on_edit_page ? 'pirep_change_status': 'pirep_submit_status']) }}
        {{ Form::button('Accept', ['type' => 'submit', 'class' => 'btn btn-success']) }}
        {{ Form::close() }}
      </td>
    @endif
    @if ($pirep->state === PirepState::PENDING || $pirep->state === PirepState::ACCEPTED)
      <td>
        {{ Form::open(['url' => route('admin.pirep.status', [$pirep->id]),
                        'method' => 'post',
                        'name' => 'reject_'.$pirep->id,
                        'id' => $pirep->id.'_reject',
                        'pirep_id' => $pirep->id,
                        'new_status' => PirepState::REJECTED,
                        'class' => $on_edit_page ? 'pirep_change_status': 'pirep_submit_status']) }}
        {{ Form::button('Reject', ['type' => 'submit', 'class' => 'btn btn-warning']) }}
        {{ Form::close() }}
      </td>
    @endif
    <td>
      {{ Form::open(['url' => route('admin.pireps.destroy', [$pirep->id]),
            'method' => 'delete',
            'name' => 'delete_'.$pirep->id,
            'id' => $pirep->id.'_delete',
            'onclick' => "return confirm('Are you sure?')"
            ]) }}
        {{ Form::button('Delete', ['type' => 'submit', 'class' => 'btn btn-danger']) }}
        {{ Form::close() }}
    </td>
    @if ($on_edit_page === false)
      <td>
        <form action="{{ route('admin.pireps.edit', [$pirep->id]) }}">
          <button type="submit" class='btn btn-info'>
            <i class="fas fa-pencil-alt"></i>&nbsp;&nbsp;Edit
          </button>
        </form>
      </td>
    @endif
    <td>
      <form action="{{ route('frontend.pireps.show', [$pirep->id]) }}" target="_blank">
        <button type="submit" class='btn btn-success'>
          <i class="fas fa-eye"></i>&nbsp;&nbsp; View Pirep
        </button>
      </form>
    </td>
  </tr>
</table>

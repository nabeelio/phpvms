@if(count($pirep->acars_logs) > 0)
  <div class="col-12">
    <table class="table table-hover" id="users-table">
      <tbody>
      @foreach($pirep->acars_logs as $log)
        <tr>
          <td nowrap="true">{{ show_datetime($log->created_at) }}</td>
          <td>{{ $log->log }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
@endif

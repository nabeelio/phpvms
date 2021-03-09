<div class="row">
  <div class="col-sm-12">
    <div class="form-container">
      <h6><i class="fas fa-clock"></i>
        &nbsp;Cron
      </h6>
      <div class="row" style="padding-top: 5px">
        <div class="col-sm-12">
          <p>A cron must be created that runs every minute calling artisan. An example is below.
            <strong><a href="{{ docs_link('cron') }}" target="_blank">See the docs</a></strong></p>
          <label style="width: 100%">
            <input type="text" value="{{ $cron_path }}" class="form-control" style="width: 100%"/>
          </label>

          @if($cron_problem_exists)
            <div class="alert alert-danger" role="alert">
              There was a problem running the cron; make sure it's setup and check logs at
              <span class="text-monospace bg-gradient-dark">storage/logs/cron.log</span>.
              <a href="{{ docs_link('cron') }}" target="_blank">See the docs</a>
            </div>
          @endif
        </div>
      </div>

      <hr>

      <div class="row" style="padding-top: 5px">
        <div class="col-sm-12">
          <h5>Web Cron</h5>
        </div>
        <div class="col-sm-6">
          <p>
            If you don't have cron access on your server, you can use a web-cron service to
            access this URL every minute. Keep it disabled if you're not using it. It's a
            unique ID that can be reset/changed if needed for security.
          </p>
        </div>
        <div class="col-sm-6 pull-right">
          <table class="table-condensed">
            <tr class="text-right">
              <td style="padding-right: 10px;" class="text-right">
                {{ Form::open(['url' => route('admin.maintenance.cron_enable'),
                            'method' => 'post']) }}
                {{ Form::button('Enable/Change ID', ['type' => 'submit', 'class' => 'btn btn-success']) }}
                {{ Form::close() }}
              </td>
              <td class="text-right">
                {{ Form::open(['url' => route('admin.maintenance.cron_disable'),
                        'method' => 'post']) }}
                {{ Form::button('Disable', ['type' => 'submit', 'class' => 'btn btn-warning']) }}
                {{ Form::close() }}
              </td>
            </tr>
          </table>
        </div>
        <div class="col-sm-12">

          <label style="width: 100%">
            <input type="text" value="{{ $cron_url }}" class="form-control" style="width: 100%"/>
          </label>
        </div>
      </div>
    </div>
  </div>
</div>

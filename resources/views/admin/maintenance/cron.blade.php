<div class="row">
  <div class="col-sm-12">
    <div class="form-container">
      <h6><i class="fas fa-clock"></i>
        &nbsp;Cron
      </h6>
      <div class="row" style="padding-top: 5px">
        <div class="col-sm-12">
          <p>A cron must be created that runs every minute calling artisan. Example:</p>
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
    </div>
  </div>
</div>

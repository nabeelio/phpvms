<div class="row">
  <div class="col-sm-12">
    <div class="form-container">
      <h6><i class="fas fa-clock"></i>
        &nbsp;Update
      </h6>
      <div class="row" style="padding-top: 5px">
        <div class="col-sm-12">
          <div class="row">
            <div class="col-sm-12">
              <p>Force new version check</p>
              {{ Form::open(['route' => 'admin.maintenance.forcecheck']) }}
              {{ Form::button('Force update check', ['type' => 'submit', 'class' => 'btn btn-success']) }}
              {{ Form::close() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

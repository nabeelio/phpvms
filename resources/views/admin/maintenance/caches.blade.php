<div class="row">
  <div class="col-sm-12">
    <div class="form-container">
      <h6><i class="fas fa-clock"></i>
        &nbsp;Reset Caches
      </h6>
      <div class="row" style="padding-top: 5px">
        <div class="col-sm-4 text-center">
          {{ Form::open(['route' => 'admin.maintenance.cache']) }}
          {{ Form::hidden('type', 'all') }}
          {{ Form::button('Clear all caches', ['type' => 'submit', 'class' => 'btn btn-success']) }}
          {{ Form::close() }}
        </div>
        <div class="col-sm-4 text-center">
          {{ Form::open(['route' => 'admin.maintenance.cache']) }}
          {{ Form::hidden('type', 'application') }}
          {{ Form::button('Application', ['type' => 'submit', 'class' => 'btn btn-success']) }}
          {{ Form::close() }}
        </div>
        <div class="col-sm-4 text-center">
          {{ Form::open(['route' => 'admin.maintenance.cache']) }}
          {{ Form::hidden('type', 'views') }}
          {{ Form::button('Views', ['type' => 'submit', 'class' => 'btn btn-success']) }}
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
</div>

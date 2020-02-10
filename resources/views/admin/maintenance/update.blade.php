<div class="row">
  <div class="col-sm-12">
    <div class="form-container">
      <h6><i class="fas fa-clock"></i>
        &nbsp;Update
      </h6>
      <div class="row" style="padding-top: 5px">
        <div class="col-sm-12">

          @if ($new_version)
            <p>An update to version {{ $new_version_tag }} is available.</p>
            {{ Form::open(['route' => 'admin.maintenance.update']) }}
            {{ Form::button('Update', ['type' => 'submit', 'class' => 'btn btn-success']) }}
            {{ Form::close() }}
          @else
            <p>There is no new version available</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

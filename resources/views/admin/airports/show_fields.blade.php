<div class="form-group col-sm-6">
  <div class="box box-solid">
    <div class="box-header with-border">
      <h3 class="box-title">ICAO</h3>
    </div>
    <div class="box-body">
      <p class="lead">{{ $airport->icao }}<strong></p>
    </div>
  </div>
</div>

<div class="form-group col-sm-6">
  <div class="box box-solid">
    <div class="box-header with-border">
      <h3 class="box-title">Coordinates</h3>
    </div>
    <div class="box-body">
      <p class="lead">{{ $airport->lat}}/{{ $airport->lon }}</p>
    </div>
  </div>
</div>

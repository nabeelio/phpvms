@extends('app')
@section('title', 'Generate OFP')

@section('content')
  <form id="sbapiform">
    <div class="row">
      <div class="col-md-12">
        <h2>Create Flight Briefing</h2>
        <div class="row">
          <div class="col-8">
            <div class="form-container">
              <h6><i class="fas fa-info-circle"></i>
                &nbsp;@lang('pireps.flightinformations')
              </h6>
              <div class="form-container-body">
                <div class="row">
                  <div class="col-sm-4">
                    <label for="orig">Departure Airport</label>
                    <input id="orig"
                           name="orig"
                           type="text"
                           class="form-control"
                           placeholder="ZZZZ"
                           maxlength="4"
                           value="{{ $flight->dpt_airport_id }}"/>
                  </div>

                  <div class="col-sm-4">
                    <label for="dest">Arrival Airport</label>
                    <input id="dest"
                           name="dest"
                           type="text"
                           class="form-control"
                           placeholder=""
                           maxlength="4"
                           value="{{ $flight->arr_airport_id }}"/>
                  </div>

                  <div class="col-sm-4">
                    <label for="type">Aircraft</label>
                    <select id="type" name="type" class="custom-select select2">
                      <option value="a320">A320</option>
                      <option value="b738">B738</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-4">
            <div class="form-container">
              <h6><i class="fas fa-info-circle"></i>
                &nbsp;Briefing Options
              </h6>
              <table class="table table-hover table-striped">
                <tr>
                  <td>Units:</td>
                  <td><select name="units">
                      <option value="KGS">KGS</option>
                      <option value="LBS" selected>LBS</option>
                    </select></td>
                </tr>
                <tr>
                  <td>Cont Fuel:</td>
                  <td><select name="contpct">
                      <option value="auto" selected>AUTO</option>
                      <option value="0">0 PCT</option>
                      <option value="0.02">2 PCT</option>
                      <option value="0.03">3 PCT</option>
                      <option value="0.05">5 PCT</option>
                      <option value="0.1">10 PCT</option>
                      <option value="0.15">15 PCT</option>
                      <option value="0.2">20 PCT</option>
                    </select></td>
                </tr>
                <tr>
                  <td>Reserve Fuel:</td>
                  <td><select name="resvrule">
                      <option value="auto">AUTO</option>
                      <option value="0">0 MIN</option>
                      <option value="15">15 MIN</option>
                      <option value="30">30 MIN</option>
                      <option value="45" selected>45 MIN</option>
                      <option value="60">60 MIN</option>
                      <option value="75">75 MIN</option>
                      <option value="90">90 MIN</option>
                    </select></td>
                </tr>
                <tr>
                  <td>Detailed Navlog:</td>
                  <td><input type="hidden" name="navlog" value="0"><input type="checkbox" name="navlog" value="1"
                                                                          checked>
                  </td>
                </tr>
                <tr>
                  <td>ETOPS Planning:</td>
                  <td><input type="hidden" name="etops" value="0"><input type="checkbox" name="etops" value="1"></td>
                </tr>
                <tr>
                  <td>Plan Stepclimbs:</td>
                  <td><input type="hidden" name="stepclimbs" value="0"><input type="checkbox" name="stepclimbs"
                                                                              value="1"
                                                                              checked>
                  </td>
                </tr>
                <tr>
                  <td>Runway Analysis:</td>
                  <td><input type="hidden" name="tlr" value="0"><input type="checkbox" name="tlr" value="1" checked>
                  </td>
                </tr>
                <tr>
                  <td>Include NOTAMS:</td>
                  <td><input type="hidden" name="notams" value="0"><input type="checkbox" name="notams" value="1"
                                                                          checked>
                  </td>
                </tr>
                <tr>
                  <td>FIR NOTAMS:</td>
                  <td><input type="hidden" name="firnot" value="0"><input type="checkbox" name="firnot" value="1"></td>
                </tr>
                <tr>
                  <td>Flight Maps:</td>
                  <td><select name="maps">
                      <option value="detail">Detailed</option>
                      <option value="simple">Simple</option>
                      <option value="none">None</option>
                    </select></td>
                </tr>
              </table>
            </div>
          </div>
        </div>

        <input type="hidden" name="airline" value="{{ $flight->airline->icao }}">
        <input type="hidden" name="fltnum" value="{{ $flight->flight_number }}">
        <input type="hidden" name="date" value="01JAN14">
        <input type="hidden" name="deph" value="12">
        <input type="hidden" name="depm" value="30">
        <input type="hidden" name="steh" value="2">
        <input type="hidden" name="stem" value="15">
        {{--<input type="hidden" name="reg" value="N123SB">
        <input type="hidden" name="selcal" value="GR-FS">--}}
        <input type="hidden" name="planformat" value="lido">
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="float-right">
          <div class="form-group">
            <input type="button"
                   onclick="simbriefsubmit('{{ $flight->id }}', '{{ url(route('frontend.simbrief.briefing', [''])) }}');"
                   class="btn btn-outline-primary"
                   value="Generate">
          </div>
        </div>
      </div>
    </div>
  </form>
@endsection
@section('scripts')
  <script src="{{public_asset('/assets/global/js/simbrief.apiv1.js')}}"></script>
@endsection

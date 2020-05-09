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
                      <option value="A306" title="A306">A306 - A300F4-600</option>
                      <option value="A310" title="A310 / CF6-80C2A2">A310 - A310-304</option>
                      <option value="A318" title="A318 / CFM56-5B9/P">A318 - A318-100</option>
                      <option value="A319" title="A319 / CFM56-5B6/2P">A319 - A319-100</option>
                      <option value="A320" title="A320 / CFM56-5B4/P">A320 - A320-200</option>
                      <option value="A321" title="A321 / CFM56-5B3/P">A321 - A321-200</option>
                      <option value="A332" title="A332 / CF6-80E1A4">A332 - A330-200</option>
                      <option value="A333" title="A333 / RR Trent 772B">A333 - A330-300</option>
                      <option value="A342" title="A342 / CFM56-5C2">A342 - A340-200</option>
                      <option value="A343" title="A343 / CFM56-5C4">A343 - A340-300</option>
                      <option value="A345" title="A345 / RB211 Trent 556-61">A345 - A340-500</option>
                      <option value="A346" title="A346 / RB211 Trent 556-61">A346 - A340-600</option>
                      <option value="A359" title="A359 / TRENT XWB-84">A359 - A350-900</option>
                      <option value="A35K" title="A35K / TRENT XWB-97">A35K - A350-1000</option>
                      <option value="A388" title="A388">A388 - A380-800</option>
                      <option value="AT72" title="AT72">AT72 - ATR72-500</option>
                      <option value="B190" title="B190 / PT6A-67D">B190 - B1900D</option>
                      <option value="B350" title="B350">B350 - KINGAIR</option>
                      <option value="B463" title="B463">B463 - BAE-146</option>
                      <option value="B703" title="B703">B703 - B707-320B</option>
                      <option value="B712" title="B712 / BR715-C1-30">B712 - B717-200</option>
                      <option value="B722" title="B722">B722 - B727-200</option>
                      <option value="B732" title="B732 / JT8D-15A">B732 - B737-200</option>
                      <option value="B733" title="B733 / CFM56-3C-1">B733 - B737-300</option>
                      <option value="B734" title="B734">B734 - B737-400</option>
                      <option value="B735" title="B735">B735 - B737-500</option>
                      <option value="B736" title="B736 / CFM56-7B22">B736 - B737-600</option>
                      <option value="BBJ1" title="B737 / CFM56-7B27">BBJ1 - B737 BBJ</option>
                      <option value="B737" title="B737 / CFM56-7B24">B737 - B737-700</option>
                      <option value="BBJ2" title="B738 / CFM56-7B27">BBJ2 - B737 BBJ2</option>
                      <option value="B738" title="B738 / CFM56-7B26">B738 - B737-800</option>
                      <option value="BBJ3" title="B739 / CFM56-7B27">BBJ3 - B737 BBJ3</option>
                      <option value="B739" title="B739 / CFM56-7B26">B739 - B737-900</option>
                      <option value="B742" title="B742 / JT9D-7F">B742 - B747-200B</option>
                      <option value="B744" title="B744 / RB211-524G/H">B744 - B747-400</option>
                      <option value="B74F" title="B744 / RB211-524G/H">B74F - B747-400F</option>
                      <option value="B748" title="B748 / GENX-2B67">B748 - B747-8</option>
                      <option value="B48F" title="B748 / GENX-2B67">B48F - B747-8F</option>
                      <option value="B752" title="B752 / PW2037">B752 - B757-200</option>
                      <option value="B75F" title="B752 / PW2037">B75F - B757-200PF</option>
                      <option value="B753" title="B753 / PW2037">B753 - B757-300</option>
                      <option value="B762" title="B762 / CF6-80C2-B2">B762 - B767-200ER</option>
                      <option value="B763" title="B763 / CF6-80C2B6F">B763 - B767-300ER</option>
                      <option value="B76F" title="B763 / CF6-80C2B6F">B76F - B767-300F</option>
                      <option value="B764" title="B764">B764 - B767-400ER</option>
                      <option value="B772" title="B772 / GE90-94B">B772 - B777-200ER</option>
                      <option value="B77L" title="B77L / GE90-110B1">B77L - B777-200LR</option>
                      <option value="B77F" title="B77L / GE90-110B1">B77F - B777-F</option>
                      <option value="B77W" title="B77W / GE90-115BL2">B77W - B777-300ER</option>
                      <option value="B788" title="B788 / GENX-1B70">B788 - B787-8</option>
                      <option value="B789" title="B789 / GENX-1B74">B789 - B787-9</option>
                      <option value="B78X" title="B78X / GENX-1B76">B78X - B787-10</option>
                      <option value="BE20" title="BE20">BE20 - KINGAIR</option>
                      <option value="C172" title="C172 / IO-360-L2A">C172 - CESSNA 172R</option>
                      <option value="C208" title="C208">C208 - CESSNA 208</option>
                      <option value="C25A" title="C25A / FJ44-2C">C25A - CITATION CJ2</option>
                      <option value="C404" title="C404">C404 - C404 TITAN</option>
                      <option value="C510" title="C510">C510 - C510 MUSTANG</option>
                      <option value="C550" title="C550">C550 - CITATION</option>
                      <option value="C56X" title="C56X / PW545A">C56X - CITATION 560XL</option>
                      <option value="C750" title="C750">C750 - CITATION X</option>
                      <option value="CL30" title="CL30 / HTF7350">CL30 - CHALLENGER</option>
                      <option value="CRJ2" title="CRJ2 / CF34-3B1">CRJ2 - CRJ-200</option>
                      <option value="CRJ7" title="CRJ7 / CF34-8C1">CRJ7 - CRJ-700</option>
                      <option value="CRJ9" title="CRJ9 / CF34-8C5">CRJ9 - CRJ-900</option>
                      <option value="CRJX" title="CRJX / CF34-8C5A1">CRJX - CRJ-1000</option>
                      <option value="DC10" title="DC10">DC10 - DC-10-30</option>
                      <option value="DC6" title="DC6 / R2800-CB16">DC6&nbsp; - DC-6</option>
                      <option value="DC85" title="DC85 / JT3D-3B">DC85 - DC-8-55</option>
                      <option value="DH8A" title="DH8A / PW120A">DH8A - DHC8-102</option>
                      <option value="DH8B" title="DH8B / PW123C">DH8B - DHC8-200</option>
                      <option value="DH8C" title="DH8C / PW123B">DH8C - DHC8-311</option>
                      <option value="DH8D" title="DH8D / PW150A">DH8D - DHC8-402</option>
                      <option value="DHC2" title="DHC2">DHC2 - BEAVER</option>
                      <option value="DHC6" title="DHC6">DHC6 - TWIN OTTER</option>
                      <option value="E13L" title="E135">E13L - EMB-135BJ</option>
                      <option value="E135" title="E135 / AE3007-A1/3">E135 - EMB-135LR</option>
                      <option value="E140" title="E135 / AE3007-A1/3">E140 - ERJ-140LR</option>
                      <option value="E145" title="E145 / AE3007-A1">E145 - EMB-145LR</option>
                      <option value="E170" title="E170 / CF34-8E5">E170 - EMB-170</option>
                      <option value="E175" title="E170 / CF34-8E5">E175 - EMB-175</option>
                      <option value="E190" title="E190 / CF34-10E6">E190 - EMB-190</option>
                      <option value="E195" title="E190 / CF34-10E7">E195 - EMB-195</option>
                      <option value="E50P" title="E50P / PW617F1-E">E50P - PHENOM 100</option>
                      <option value="E55P" title="E55P / PW535E">E55P - PHENOM 300</option>
                      <option value="EA50" title="EA50 / PW610F">EA50 - ECLIPSE 550</option>
                      <option value="F50" title="F50">F50&nbsp; - FOKKER F50</option>
                      <option value="FA50" title="FA50 / TFE 731-40">FA50 - FALCON 50EX</option>
                      <option value="GLF4" title="GLF4">GLF4 - GULFSTREAM</option>
                      <option value="H25B" title="H25B">H25B - HAWKER 800A</option>
                      <option value="JS41" title="JS41">JS41 - BAE JS-41</option>
                      <option value="L101" title="L101 / RB211-524B">L101 - L1011-500</option>
                      <option value="LJ25" title="LJ25 / CJ-610-8A">LJ25 - LEARJET 25</option>
                      <option value="LJ45" title="LJ45">LJ45 - LEARJET 45</option>
                      <option value="MD11" title="MD11 / CF6-80C2D1F">MD11 - MD-11</option>
                      <option value="MD1F" title="MD11 / CF6-80C2D1F">MD1F - MD-11F</option>
                      <option value="MD82" title="MD82 / JT8D-217">MD82 - DC-9-82</option>
                      <option value="MD83" title="MD83 / JT8D-219">MD83 - DC-9-83</option>
                      <option value="MD88" title="MD88 / JT8D-219">MD88 - MD-88</option>
                      <option value="MD90" title="MD90">MD90 - MD-90-30</option>
                      <option value="PC12" title="PC12 / PT6A-66D">PC12 - PILATUS PC12</option>
                      <option value="RJ1H" title="RJ1H">RJ1H - AVRO RJ100</option>
                      <option value="RJ70" title="RJ70">RJ70 - AVRO RJ70</option>
                      <option value="RJ85" title="RJ85">RJ85 - AVRO RJ85</option>
                      <option value="SF34" title="SF34 / GE CT7-9B">SF34 - SAAB 340B</option>
                      <option value="SF50" title="SF50 / FJ33-5A">SF50 - VISION JET</option>
                      <option value="SW4" title="SW4 / TPE-331">SW4&nbsp; - METROLINER</option>
                      <option value="T154" title="T154">T154 - TU-154B2</option>
                      <option value="TBM9" title="TBM9 / PT6A-66D">TBM9 - TBM 900</option>
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

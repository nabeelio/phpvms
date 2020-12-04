@extends('app')
@section('title', 'Generate OFP')

@section('content')
  @php
	$fareSvc = app(App\Services\FareService::class);
	$flight = $fareSvc->getReconciledFaresForFlight($flight);		
	@endphp
  <form id="sbapiform">
    <div class="row">
      <div class="col-md-12"><h2>Create Flight Briefing</h2>
        <div class="row">
          <div class="col-8">
            <div class="form-container"><h6><i class="fas fa-info-circle"></i>&nbsp;@lang('pireps.flightinformations')</h6>
              <div class="form-container-body">
                <div class="row">
                  <div class="col-sm-4">
					        <label for="AircraftSelect">Aircraft Selection</label>
						      <select name="AircraftSelect" class="form-control select2" id="AircraftSelect" onchange="SplitSelection()">
						      <option value="ZZZZ,ZZZZZ" title="ZZZZ">Please Select An Aircraft</option>
						      @foreach($flight->subfleets as $SFID)
						      @php $AircraftList = App\Models\Aircraft::where('subfleet_id', $SFID->id)->orderBy('icao')->get(); @endphp
   							    @foreach ($AircraftList as $AC)
							        <option value="{{ $AC->icao }},{{ $AC->registration }}#{{ $AC->subfleet_id }}">[ {{ $AC->icao }} ] {{ $AC->registration }} @if($AC->registration <> $AC->name) '{{ $AC->name }}' @endif</option>
							      @endforeach
						     @endforeach
						</select>
	
					<!-- Below value fields may be used to check SimBrief available types
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
                      <option value="DC6"  title="DC6 / R2800-CB16">DC6 - DC-6</option>
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
                      <option value="F50"  title="F50">F50 - FOKKER F50</option>
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
                      <option value="SW4"  title="SW4 / TPE-331">SW4 - METROLINER</option>
                      <option value="T154" title="T154">T154 - TU-154B2</option>
                      <option value="TBM9" title="TBM9 / PT6A-66D">TBM9 - TBM 900</option>
                    </select>
					    -->
              </div>
				  <div class="col-sm-4">
            <label for="type">ICAO Type</label>
            <input id="type" name="type" type="text" class="form-control" placeholder="ZZZZ" maxlength="4" />
          </div>
					
				  <div class="col-sm-4">
            <label for="reg">Registration</label>
            <input id="reg" name="reg" type="text" class="form-control" placeholder="ZZZZZ" maxlength="6" />
          </div>
        </div>
				<div class="row"><br></div>
				<div class="row">
				  <div class="col-sm-4">
            <label for="orig">Departure Airport</label>
            <input id="orig" name="orig" type="text" class="form-control" maxlength="4" value="{{ $flight->dpt_airport_id }}" />
          </div>
          <div class="col-sm-4">
            <label for="dest">Arrival Airport</label>
            <input id="dest" name="dest" type="text" class="form-control" maxlength="4" value="{{ $flight->arr_airport_id }}" />
          </div>
					@php if($flight->alt_airport_id) { $ALTN = $flight->alt_airport_id ; } else { $ALTN = 'AUTO' ; } @endphp
				  <div class="col-sm-4">
            <label for="altn">Alternate Airport</label>
            <input id="altn" name="altn" type="text" class="form-control" maxlength="4" value="{{ $ALTN }}"/>
          </div>
				</div>
				<div class="row"><br></div>
				<div class="row">
					<div class="col-sm-8">
					   <label for="route">Preferred Company Route</label>
					   <input id="route" name="route" type="text" class="form-control" maxlength="1000" value="{{ $flight->route }}" />
					</div>					
					<div class="col-sm-4">
					   <label for="fl">Preferred Flight Level</label>
             <input id="fl" name="fl" type="text" class="form-control" maxlength="5" value="{{ $flight->level }}" />
					</div>
				</div>
				<div class="row"><br></div>
				<div class="row">
					<div class="col-sm-4">
						<label for="std">Scheduled Departure Time (UTC)</label>
						<input id="std" type="text" class="form-control" maxlength="4" value="{{ $flight->dpt_time }}" disabled/>
					</div>
					<div class="col-sm-4">
						<label for="etd">Estimated Departure Time (UTC)</label>
						<input id="etd" type="text" class="form-control" maxlength="4" disabled/>
					</div>
					<div class="col-sm-4">
						<label for="dof">Date Of Flight (UTC)</label>
						<input id="dof" type="text" class="form-control" maxlength="4" disabled/>
					</div>		
				</div>
				<hr>
				<h6><i class="fas fa-info-circle"></i>&nbsp;Load Information For Assigned Fleet(s)</h6>
				@php
					$FKMin = $flight->load_factor - $flight->load_factor_variance ;
					$FKMax = $flight->load_factor + $flight->load_factor_variance ;
					$FKRandomLoad = rand($FKMin, $FKMax);
				@endphp	
				@foreach($flight->subfleets as $SUB)
					<div class="row">
						<div class="col-sm-8">Configuration and Load Figures for <b>&nbsp;{{ $SUB->name }}&nbsp;</b> ;</div>
					</div>
					<br>
					<div class="row">
					@foreach($SUB->fares as $SUBfares)
						@if($SUBfares->capacity > 0)
						@php $RandomLoadPerFare = round(($SUBfares->capacity * $FKRandomLoad) /100) @endphp
						<div class="col-sm-4">
              <label for="CapFare{{ $SUBfares->id }}">{{ $SUBfares->name }} Capacity</label><br>
              <input id="CapFare{{ $SUBfares->id }}" type="text" value="{{ $SUBfares->capacity }}" disabled/>
						<br>
							<label for="LoadFare{{ $SUBfares->id }}">{{ $SUBfares->name }} Load</label><br>
							<input id="LoadFare{{ $SUBfares->id }}" type="text" value="{{ $RandomLoadPerFare }}" disabled/>
						</div>
						@endif
					@endforeach
					</div>
					<hr>
				@endforeach
				<div class="row"><div class="col-sm-12">Selected Aircraft's Total Pax/Cargo Load Will Be Used For Flight Planning</div></div>
      </div>
    </div>
  </div>
  @php $GetSubFleetIDs = DB::table('flight_subfleet')->where('flight_id', $flight->id)->select('id', 'subfleet_id')->get() @endphp
	@foreach($GetSubFleetIDs as $SubFleetIDs)
	  @php $GetMaxCap = DB::table('subfleet_fare')->where('subfleet_id', $SubFleetIDs->subfleet_id)->sum('capacity') @endphp
		@php $CalcRandomLoad = ceil(($GetMaxCap * $FKRandomLoad) /100) @endphp
		@php if($CalcRandomLoad > 900) { $SimBriefLoadType = "cargo" ; echo "<input type='hidden' name='pax' value='0' maxlength='3'>" ; } else { $SimBriefLoadType = "pax" ; } @endphp								
		<input id="LoadSF{{ $SubFleetIDs->subfleet_id }}" type="hidden" name="{{ $SimBriefLoadType }}" class="form-control" value="{{ $CalcRandomLoad }}" disabled/>
  @endforeach			
		<input type="hidden" name="airline" value="{{ $flight->airline->icao }}">
    <input type="hidden" name="fltnum" value="{{ $flight->flight_number }}">
    <input type="hidden" id="date" name="date" maxlength="9">
		<input type="hidden" id="deph" name="deph" maxlength="2">
    <input type="hidden" id="depm" name="depm" maxlength="2">
		<input type="hidden" id="steh" name="steh" maxlength="2">
		<input type="hidden" id="stem" name="stem" maxlength="2">
    <input type="hidden" name="selcal" value="BK-FS">
    <input type="hidden" name="planformat" value="lido">
		<input type="hidden" name="omit_sids" value="0">
		<input type="hidden" name="omit_stars" value="0">
		<input type="hidden" name="cruise" value="CI">
		<input type="hidden" name="civalue" value="AUTO">
			
  <div class="col-4">
	  <div class="form-container">
      <h6><i class="fas fa-info-circle"></i>&nbsp;Planning Options</h6>	
				<table class="table table-hover table-striped">
        <tr>
          <td>Cont Fuel:</td>
        <td>
         	<select name="contpct" class="form-control">
           	<option value="auto">AUTO</option>
            <option value="0">0 PCT</option>
            <option value="0.02">2 PCT</option>
            <option value="0.03">3 PCT</option>
            <option value="0.05" selected>5 PCT</option>
            <option value="0.1">10 PCT</option>
            <option value="0.15">15 PCT</option>
            <option value="0.2">20 PCT</option>
        	</select>
        </td>
        </tr>
        <tr>
          <td>Reserve Fuel:</td>
          <td>
            <select name="resvrule" class="form-control">
              <option value="auto">AUTO</option>
              <option value="0">0 MIN</option>
              <option value="15">15 MIN</option>
              <option value="30" selected>30 MIN</option>
              <option value="45">45 MIN</option>
              <option value="60">60 MIN</option>
              <option value="75">75 MIN</option>
              <option value="90">90 MIN</option>
             </select>
           </td>
          </tr>
          <tr>
            <td>SID/STAR Type:</td>
          <td>
					  <select name="find_sidstar" class="form-control">
						  <option value="C">Conventinoal</option>
							<option value="R" selected>RNAV</option>
						</select>
          </td>
          </tr>
          <tr>
            <td>Plan Stepclimbs:</td>
          <td>
					  <select id="stepclimbs" name="stepclimbs" class="form-control" onchange="DisableFL()">
							<option value="0" selected>Disabled</option>
							<option value="1">Enabled</option>
						</select>
          </td>
          </tr>				
          <tr>
            <td>ETOPS Planning:</td>
          <td>
						<select name="etops" class="form-control">
							<option value="0" selected>Disabled</option>
							<option value="1">Enabled</option>
						</select>
					</td>
          </tr>							
				</table>				
			</div>
			<br>
      <div class="form-container">
        <h6><i class="fas fa-info-circle"></i>&nbsp;Briefing Options</h6>
          <table class="table table-hover table-striped">
            <tr>
              <td>Units:</td>
            <td>
              <select name="units" class="form-control">
                <option value="KGS" selected>KGS</option>
                <option value="LBS">LBS</option>
               </select>
            </td>
            </tr>
            <tr>
              <td>Detailed Navlog:</td>
             <td>
					      <select name="navlog" class="form-control">
						      <option value="0">Disabled</option>
						      <option value="1" selected>Enabled</option>
					      </select>
             </td>
             </tr>
             <tr>
                <td>Runway Analysis:</td>
             <td>
					      <select name="tlr" class="form-control">
						      <option value="0">Disabled</option>
						      <option value="1" selected>Enabled</option>
					      </select>
             </td>
             </tr>
             <tr>
             <td>Include NOTAMS:</td>
             <td>
					      <select name="notams" class="form-control">
						      <option value="0">Disabled</option>
						      <option value="1" selected>Enabled</option>
					      </select>
              </td>
              </tr>
              <tr>
                <td>FIR NOTAMS:</td>
              <td>
					      <select name="firnot" class="form-control">
						        <option value="0" selected>Disabled</option>
						        <option value="1">Enabled</option>
					      </select>
              </td>
              </tr>
              <tr>
                <td>Flight Maps:</td>
                <td>
                  <select name="maps" class="form-control">
                    <option value="detail" selected>Detailed</option>
                    <option value="simple">Simple</option>
                    <option value="none">None</option>
                  </select>
                </td>
               </tr>
              </table>
            </div>
          </div>
        </div>
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
	<script type="text/javascript">
		// ******
		// Disable Submitting a fixed flight level for Stepclimb option to work
		// Script is related to Plan Step Climbs selection
		function DisableFL() {
 			var climb = document.getElementById("stepclimbs").value;
			if (climb == "0") {document.getElementById("fl").disabled = false};
			if (climb == "1") {document.getElementById("fl").disabled = true};	
			}
	</script>
	<script type="text/javascript">
		// ******
		// Get current UTC time, add 45 minutes to it and format according to Simbrief API
		// Script also rounds the minutes to nearest 5 to avoid a Departure time like 1538 ;)
		// If you need to reduce the margin of 45 mins, change value below
		var d = new Date() ;
		var months = ["JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC"] ;
		d.setMinutes(d.getMinutes() + 45) ; // Change the value here
		var deph = ("0" + d.getUTCHours(d)).slice(-2) ;
		var depm = d.getUTCMinutes(d);
		if(depm < 55) { depm = Math.ceil(depm/5)*5;}
		if(depm > 55) { depm = Math.floor(depm/5)*5;}
		depm = ("0" + depm).slice(-2) ;
		dept = deph + depm ;
		var dof = ("0" + d.getUTCDate()).slice(-2) + months[d.getUTCMonth()] + d.getUTCFullYear() ;
		document.getElementById("dof").setAttribute('value',dof) ;
		document.getElementById("etd").setAttribute('value',dept) ;
		document.getElementById("date").setAttribute('value',dof) ; // Sent to Simbrief
		document.getElementById("deph").setAttribute('value',deph) ; // Sent to SimBrief
		document.getElementById("depm").setAttribute('value',depm) ; // Sent to SimBrief
	</script>
	<script type="text/javascript">
		// ******
		// Calculate the Scheduled Enroute Time for Simbrief API
		// Your PHPVMS flight_time value must be from BLOCK to BLOCK
		// Including departure and arrival taxi times
		// If this value is not correctly calculated and configured
		// Simbrief CI (Cost Index) calculation will not provide realistic results
		var num = {{ $flight->flight_time }} ;
		var hours = (num / 60);
		var rhours = Math.floor(hours);
		var minutes = (hours - rhours) * 60;
		var rminutes = Math.round(minutes);
		document.getElementById("steh").setAttribute('value',rhours) ; // Sent to Simbrief
		document.getElementById("stem").setAttribute('value',rminutes) ; // Sent to Simbrief
	</script>
	<script type="text/javascript">
		// ******
		// Splits the Aircraft Selection value and sends the results to proper form fields
		// Output values used for Simbrief integration and load calculations/selections
		// Also includes some ICAO Type Corrections for SimBrief Aircraft Types
		function SplitSelection() {
			@foreach($flight->subfleets as $SubFleets)
				var SubFleetsSB = "LoadSF".concat({{ $SubFleets->id }});
				document.getElementById(SubFleetsSB).disabled = true;
			@endforeach
			var str = document.getElementById("AircraftSelect").value;
  		var d1 = str.search(",");
			var d2 = str.search("#");
  		var icao = str.slice(0, d1);
  		var registration = str.slice(d1+1,d2);
			var subfleetid = str.slice(d2+1);
			var SelectedSubFleetSB = "LoadSF".concat(subfleetid);
				if (icao == "A20N") {icao = "A320"}; // Correction for A320 NEO
				if (icao == "A21N") {icao = "A321"}; // Correction for A321 NEO
				if (icao == "B77L") {icao = "B77F"}; // Correction for B777 Freighter
				if (icao == "B773") {icao = "B77W"}; // Correction for B777-300
  		document.getElementById("type").value = icao;
  		document.getElementById("reg").value = registration;
			document.getElementById(SelectedSubFleetSB).disabled = false;
		}
	</script>
@endsection

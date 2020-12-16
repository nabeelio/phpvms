@extends('app')
@section('title', 'SimBrief Flight Planning')

@section('content')

@php
	$fareSvc = app(App\Services\FareService::class);
	$flight = $fareSvc->getReconciledFaresForFlight($flight);		
@endphp

{{-- SIMPLE ERROR PROTECTION; If the Aircraft_ID is not passed stop procession code dispay an error message --}}
@if(app('request')->input('aircraft_id'))

@php 
	$selectedaircraft = app('request')->input('aircraft_id') ;
	$aircraftdetails = DB::table('aircraft')->select('registration', 'icao', 'iata', 'subfleet_id')->where('id', $selectedaircraft)->get() ;
@endphp

{{-- Get Aircraft Details and Apply ICAO Type Corrections For SimBrief --}}
@foreach($aircraftdetails as $acdetails)
	@php 
		$simbrieftype = $acdetails->icao ;
		$subflid = $acdetails->subfleet_id ;
		if($acdetails->icao == 'A20N') { $simbrieftype = 'A320' ; }
		if($acdetails->icao == 'A21N') { $simbrieftype = 'A321' ; }
		if($acdetails->icao == 'B77L') { $simbrieftype = 'B77F' ; }
		if($acdetails->icao == 'B773') { $simbrieftype = 'B77W' ; }
		if($acdetails->icao == 'E35L') { $simbrieftype = 'E135' ; }
	@endphp
@endforeach

@php if($flight->alt_airport_id) { $altn = $flight->alt_airport_id ; } else { $altn = 'AUTO' ; } @endphp

{{-- Define The Random Load Factor, Get Max Capacity of Selected SubFleet and Generate Load --}}
@php
	$fkmin = $flight->load_factor - $flight->load_factor_variance ;
	$fkmax = $flight->load_factor + $flight->load_factor_variance ;
	if($fkmin < 1) { $fkmin = 1 ; }	
	if($fkmax > 100) { $fkmax = 100 ; }
	$fkrandomload = rand($fkmin, $fkmax);	
@endphp

<form id="sbapiform">
<div class="row">
<div class="card">
	<div class="col-md-12">
		<h2>Create Flight Briefing Package</h2>
		<div class="row">
			<div class="col-8">
				<div class="form-container">
					<div class="form-container-body">
					<h6><i class="fas fa-info-circle"></i>&nbsp;Aircraft Details</h6>
					<div class="row">
						<div class="col-sm-4">
							<label for="type">Type</label>
							<input type="text" class="form-control" value="{{ $acdetails->icao }}" maxlength="4" disabled/>
							<input type="hidden" id="type" name="type"  class="form-control" value="{{ $simbrieftype }}" maxlength="4" />
						</div>
						<div class="col-sm-4">
							<label for="reg">Registration</label>
							<input type="text" class="form-control" value="{{ $acdetails->registration }}" maxlength="6" disabled/>
							<input type="hidden" id="reg" name="reg" value="{{ $acdetails->registration }}" />
						</div>
					</div>
					<br>
					</div>
					
					<div class="form-container-body">
					<h6><i class="fas fa-info-circle"></i>&nbsp;@lang('pireps.flightinformations') for <b>{{ $flight->airline->icao }} {{ $flight->flight_number }}</b></h6>
					<div class="row">
						<div class="col-sm-4">
							<label for="dorig">Departure Airport</label>
							<input id="dorig" type="text" class="form-control" maxlength="4" value="{{ $flight->dpt_airport_id }}" disabled/>
							<input id="orig" name="orig" type="hidden" maxlength="4" value="{{ $flight->dpt_airport_id }}" />
						</div>
						<div class="col-sm-4">
							<label for="ddest">Arrival Airport</label>
							<input id="ddest" type="text" class="form-control" maxlength="4" value="{{ $flight->arr_airport_id }}" disabled/>
							<input id="dest" name="dest" type="hidden" maxlength="4" value="{{ $flight->arr_airport_id }}" />
						</div>
						<div class="col-sm-4">
							<label for="altn">Alternate Airport</label> 
							<input id="altn" name="altn" type="text" class="form-control" maxlength="4" value="{{ $altn }}" />
 						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-8">
							<label for="route">Preferred Company Route</label>
							<input id="route" name="route" type="text" class="form-control" placeholder="" maxlength="1000" value="{{ $flight->route }}" />
						</div>
						<div class="col-sm-4">
							<label for="fl">Preferred Flight Level</label>
							<input id="fl" name="fl" type="text" class="form-control" placeholder="" maxlength="5" value="{{ $flight->level }}" />
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-4">
							<label for="std">Scheduled Departure Time (UTC)</label>
							<input id="std" type="text" class="form-control" placeholder="" maxlength="4" value="{{ $flight->dpt_time }}" disabled/>
						</div>
						<div class="col-sm-4">
							<label for="etd">Estimated Departure Time (UTC)</label>
							<input id="etd" type="text" class="form-control" placeholder="" maxlength="4" disabled/>
						</div>
						<div class="col-sm-4">
							<label for="dof">Date Of Flight (UTC)</label>
							<input id="dof" type="text" class="form-control" placeholder="" maxlength="4" disabled/>
						</div>
					</div>
					<br>
					</div>
					
					<div class="form-container-body">
					{{-- Get All Subfleets from flight and generate random load for each fare type of selected SubFleet --}}
					@foreach($flight->subfleets as $subfleet)
						@if($subfleet->id == $subflid)
						<h6><i class="fas fa-info-circle"></i>&nbsp;Configuration And Load Information For <b>{{ $subfleet->name }} ; {{ $acdetails->registration }}</b></h6>
						{{-- Generate Load Figures --}}
						<div class="row">
						@php $loadarray = [] ; @endphp
						@foreach($subfleet->fares as $fare)
							@if($fare->capacity > 0)
								@php 
									$randomloadperfare = ceil(($fare->capacity * $fkrandomload) /100);
									$loadarray[] = ['SeatType' => $fare->code];
									$loadarray[] = ['SeatLoad' => $randomloadperfare];
								@endphp
								<div class="col-sm-4">
									<label for="LoadFare{{ $fare->id }}">{{ $fare->name }} Load [ Max: {{ number_format($fare->capacity) }} ]</label>
									<input id="LoadFare{{ $fare->id }}" type="text" class="form-control" value="{{ number_format($randomloadperfare) }} @if($randomloadperfare > '900') {{ setting('units.weight') }} @endif" disabled/>
								</div>
							@endif
						@endforeach
						@php 
							$loadcollection = collect($loadarray) ; 
							$totalgenload = $loadcollection->sum('SeatLoad') ;
						@endphp
						</div>
						{{-- End Generate Load Figures --}}
												
						@php $pxweight = '208' ; @endphp {{-- Just For Safety  --}}
						@if($totalgenload < '900')
							{{-- >Passenger Flight --}}
							@if($flight->flight_type == 'C')
								<input type="hidden" name="acdata" value="{'paxwgt':197}"> {{-- Use and Send Charter Pax Weights --}}
								@php $pxweight = '197' ; @endphp
							@else
								<input type="hidden" name="acdata" value="{'paxwgt':219}"> {{-- Use and Send Scheduled Pax Weights Type J/G and all the rest --}}
								@php $pxweight = '219' ; @endphp											
							@endif
							<br>
							<div class="row">
								<div class="col-sm-4">
									@if(setting('units.weight') === 'kg')
										@php $estimatedpayload = number_format(round(($pxweight * $totalgenload) / 2.2)) ; @endphp
									@else
										@php $estimatedpayload = number_format(round($pxweight * $totalgenload)) ; @endphp
									@endif
									<label for="EstimatedLoad">Estimated Load For {{ $totalgenload }} Pax</label>
									<input id="EstimatedLoad" type="text" class="form-control" value="{{ $estimatedpayload }} {{ setting('units.weight') }}" disabled/>
								</div>
							</div>
							<input type="hidden" id="pax" name="pax" class="form-control" value="{{ $totalgenload }}"/>
						@else
							{{-- This is A Cargo Flight So Send Pax 0 to avoid SimBrief auto generation --}}
							<input type='hidden' id="pax" name='pax' value='0' maxlength='3'>
							<input type='hidden' id="cargo" name='cargo' value="{{ $totalgenload }}" maxlength='7'>		
						@endif
						@endif		
					@endforeach
					{{-- END Get All Subfleets from flight And Generate Random Load For Each Fare Type of Selected SubFleet --}}
					<br>
						<div class="row">
							@php
							$flightype = 'SimBrief Standard';
							if($flight->flight_type == 'J') { $flightype = 'Schedule All Adult Pax' ;}
							if($flight->flight_type == 'G') { $flightype = 'Schedule All Adult Pax' ;}
							if($flight->flight_type == 'C') { $flightype = 'Charter All Adult Pax' ;}
							if($flight->flight_type == 'F') { $flightype = 'Only Cargo' ;}
							if($flight->flight_type == 'A') { $flightype = 'Only Cargo' ;}
							if($flight->flight_type == 'H') { $flightype = 'Only Cargo' ;}
							@endphp
							<div class="col-sm-12">&bull; <b>{{ $flightype }}</b> Weights Will Be Used For Flight Planning</div>
						</div>
					</div>
				</div>
			</div>
			{{-- Generate The MANUAL DISPATCH REMARK to send random load distribution to OFP --}}
			@php
				$loaddisttxt =  "Load Distribution " ;
				$loaddist = implode(' ', array_map(
    								function ($v, $k) {
        								if(is_array($v)){
            								return implode('&'.' '.':', $v);
        								}else{
            								return $k.':'.$v;
        								}
									}, 
    							$loadarray,	array_keys($loadarray)
							));
			@endphp
			{{-- END Generate The MANUAL DISPATCH REMARK to send random load distribution to OFP --}}		
		
			<input type="hidden" name="manualrmk" value="{{ $loaddisttxt }}{{ $loaddist }}">
            <input type="hidden" name="airline" value="{{ $flight->airline->icao }}">
            <input type="hidden" name="fltnum" value="{{ $flight->flight_number }}">
			<input type="hidden" id="steh" name="steh" maxlength="2">
			<input type="hidden" id="stem" name="stem" maxlength="2">
			<input type="hidden" id="date" name="date" maxlength="9">
			<input type="hidden" id="deph" name="deph" maxlength="2">
            <input type="hidden" id="depm" name="depm" maxlength="2">
            <input type="hidden" name="selcal" value="BK-FS">
            <input type="hidden" name="planformat" value="lido">
			<input type="hidden" name="omit_sids" value="0">
			<input type="hidden" name="omit_stars" value="0">
			<input type="hidden" name="cruise" value="CI">
			<input type="hidden" name="civalue" value="AUTO">		
			
			{{-- END Generate The MANUAL DISPATCH REMARK to send random load distribution to OFP --}}
			<div class="col-4">
				<div class="form-container">
				<div class="form-container-body">
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
				<div class="form-container-body">
					<h6><i class="fas fa-info-circle"></i>&nbsp;@lang('stisla.briefingoptions')</h6>	
					<table class="table table-hover table-striped">
						<tr>
							<td>Units:</td>
							<td>
								<select id="kgslbs" name="units" class="form-control">
									@if(setting('units.weight') === 'kg')
									<option value="KGS" selected>KGS</option>
									<option value="LBS">LBS</option>
									@else
									<option value="KGS">KGS</option>
									<option value="LBS" selected>LBS</option>
									@endif
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
				<br>
				<div class="form-container-body">
					<div class="float-right">
						<div class="form-group">
            				<input type="button" onclick="simbriefsubmit('{{ $flight->id }}', '{{ url(route('frontend.simbrief.briefing', [''])) }}');" class="btn btn-primary" value="Generate">
						</div>
					</div>	
				</div>
				</div>
            </div>
		</div>
	</div>
</div>
</div>
</form>
@else
<div class="row">
	<div class="card">
		<div class="card-header"><h4>ERROR !!!</h4></div>
		<div class="card-body">Aircraft ID not available !!! Please select an aircraft to proceed SimBrief Flight Planning.</div>
	</div>
</div>
@endif
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
@endsection

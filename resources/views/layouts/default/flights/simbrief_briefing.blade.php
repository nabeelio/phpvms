@extends('app')
@section('title', 'Briefing')

@section('content')
  <div class="row">
    <div class="col-sm-9">
      <h2>{{ $simbrief->xml->general->icao_airline }}{{ $simbrief->xml->general->flight_number }}
        : {{ $simbrief->xml->origin->icao_code }} to {{ $simbrief->xml->destination->icao_code }}</h2>
    </div>
    <div class="col-sm-3">
      @if (empty($simbrief->pirep_id))
        <a class="btn btn-outline-info pull-right btn-lg"
           style="margin-top: -10px;margin-bottom: 5px"
           href="{{ url(route('frontend.simbrief.prefile', [$simbrief->id])) }}">Prefile PIREP</a>
      @endif
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">

      <div class="row">
        <div class="col-6">
          <div class="form-container">
            <h6><i class="fas fa-info-circle"></i>
              &nbsp;Dispatch Information
            </h6>
            <div class="form-container-body">
              <div class="row">
                <div class="col-4 text-center">
                  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Flight</p>
                    <p class="border border-dark rounded p-1 small text-monospace">
                      {{ $simbrief->xml->general->icao_airline }}{{ $simbrief->xml->general->flight_number }}</p>
                  </div>
                </div>

                <div class="col-4 text-center">
                  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Departure</p>
                    <p class="border border-dark rounded p-1 small text-monospace">
                      {{ $simbrief->xml->origin->icao_code }}@if(!empty($simbrief->xml->origin->plan_rwy))
                        /{{ $simbrief->xml->origin->plan_rwy }}
                      @endif
                    </p>
                  </div>
                </div>

                <div class="col-4 text-center">
                  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Arrival</p>
                    <p class="border border-dark rounded p-1 small text-monospace">
                      {{ $simbrief->xml->destination->icao_code }}@if(!empty($simbrief->xml->destination->plan_rwy))
                        /{{ $simbrief->xml->destination->plan_rwy }}
                      @endif
                    </p>
                  </div>
                </div>
              </div>

              <hr/>

              <div class="row">

                <div class="col-4 text-center">
                  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Aircraft</p>
                    <p class="border border-dark rounded p-1 small text-monospace">
                      {{ $simbrief->xml->aircraft->name }}</p>
                  </div>
                </div>

                <div class="col-4 text-center">
                  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Enroute Time</p>
                    <p class="border border-dark rounded p-1 small text-monospace">
                      @minutestotime($simbrief->xml->times->sched_time_enroute / 60)</p>
                  </div>
                </div>

                <div class="col-4 text-center">
                  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Cruise Altitude</p>
                    <p class="border border-dark rounded p-1 small text-monospace">
                      {{ $simbrief->xml->general->initial_altitude }}</p>
                  </div>
                </div>

              </div>

              <hr/>

              @if (!empty($simbrief->xml->general->dx_rmk))
                <div class="row">
                  <div class="col-12">
                    <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Dispatcher Remarks</p>
                      <p class="border border-dark rounded p-1 small text-monospace">
                        {{ $simbrief->xml->general->dx_rmk  }}</p>
                    </div>
                  </div>
                </div>
              @endif

              @if (!empty($simbrief->xml->general->sys_rmk))
                <div class="row">
                  <div class="col-12">
                    <div><p class="small text-uppercase pb-sm-0 mb-sm-1">System Remarks</p>
                      <p class="border border-dark rounded p-1 small text-monospace">
                        {{ $simbrief->xml->general->sys_rmk  }}</p>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>

          <div class="form-container">
            <h6><i class="fas fa-info-circle"></i>
              &nbsp;Flight Plan
            </h6>
            <div class="form-container-body">
              <div class="row">
                <div class="col-12">
                  <p class="border border-dark rounded p-1 small text-monospace">
                    {!!  str_replace("\n", "<br>", $simbrief->xml->atc->flightplan_text) !!}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="form-container">
            <h6><i class="fas fa-info-circle"></i>&nbsp;Weather</h6>
            <div class="form-container-body">
              <div class="row">
                <div class="col-12">
                  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Departure METAR</p>
                    <p class="border border-dark rounded p-1 small text-monospace">{{ $simbrief->xml->weather->orig_metar }}</p>
                  </div>
                  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Departure TAF</p>
                    <p class="border border-dark rounded p-1 small text-monospace">{{ $simbrief->xml->weather->orig_taf }}</p>
                  </div>
                  <hr/>
                  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Destination METAR</p>
                    <p class="border border-dark rounded p-1 small text-monospace">{{ $simbrief->xml->weather->dest_metar }}</p>
                  </div>
                  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Destination TAF</p>
                    <p class="border border-dark rounded p-1 small text-monospace">{{ $simbrief->xml->weather->dest_taf }}</p>
                  </div>
				  <hr/>
				  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Alternate METAR</p>
                    <p class="border border-dark rounded p-1 small text-monospace">{{ $simbrief->xml->weather->altn_metar }}</p>
                  </div>
                  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Alternate TAF</p>
                    <p class="border border-dark rounded p-1 small text-monospace">{{ $simbrief->xml->weather->altn_taf }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-6">
          <div class="form-container">
            <h6><i class="fas fa-info-circle"></i>
              &nbsp;Download Flight Plan
            </h6>
            <div class="form-container-body">
              <div class="row">
                <div class="col-12">
                  <select id="download_fms_select" class="select2 custom-select">
                    @foreach($simbrief->files as $fms)
                      <option value="{{ $fms['url'] }}">{{ $fms['name'] }}</option>
                    @endforeach
                  </select>
                  <br/>
                  <input id="download_fms"
                         type="submit"
                         class="btn btn-outline-primary pull-right"
                         value="Download"/>
                </div>
              </div>
            </div>
          </div>
          
          <div class="form-container">
            <h6><i class="fas fa-info-circle"></i>&nbsp;Prefile ATC Flight Plan</h6>
            <div class="form-container-body">
              <div class="row">
                <div class="col-4" align="center">
				          @php
					        $str = $simbrief->xml->aircraft->equip ;
					        $wc = stripos($str,"-");
					        $tr = stripos($str,"/");
					        $wakecat = substr($str,0,$wc);
					        $equipment = substr($str,$wc+1,$tr-2);
					        $transponder = substr($str,$tr+1);
							    function secstohhmm($seconds) {
                      			$seconds = round($seconds);
                      			$hhmm = sprintf('%02d%02d', ($seconds/ 3600),($seconds/ 60 % 60));
                      			echo $hhmm ;
                    		}
				          @endphp
				    <form action="https://fpl.ivao.aero/api/fp/load" method="POST" target="_blank">
            		  <input type="hidden" name="CALLSIGN" value="{{ $simbrief->xml->atc->callsign }}" />
            		  <input type="hidden" name="RULES" value="I" />
           			  <input type="hidden" name="FLIGHTTYPE" value="N" />
            		  <input type="hidden" name="NUMBER" value="1" />
            		  <input type="hidden" name="ACTYPE" value="{{ $simbrief->xml->aircraft->icaocode }}" />
            		  <input type="hidden" name="WAKECAT" value="{{ $wakecat }}" />
            		  <input type="hidden" name="EQUIPMENT" value="{{ $equipment }}" />
            		  <input type="hidden" name="TRANSPONDER" value="{{ $transponder }}" />
           			  <input type="hidden" name="DEPICAO" value="{{ $simbrief->xml->origin->icao_code}}" />
            		  <input type="hidden" name="DEPTIME" value="{{ date('Hi', $simbrief->xml->times->est_out->__toString())."" }}" />
            		  <input type="hidden" name="SPEEDTYPE" value="{{ $simbrief->xml->atc->initial_spd_unit }}" />
            		  <input type="hidden" name="SPEED" value="{{ $simbrief->xml->atc->initial_spd }}" />
            		  <input type="hidden" name="LEVELTYPE" value="{{ $simbrief->xml->atc->initial_alt_unit }}" />
            		  <input type="hidden" name="LEVEL" value="{{ $simbrief->xml->atc->initial_alt }}" />
            		  <input type="hidden" name="ROUTE" value="{{ $simbrief->xml->general->route_ifps }}" />
            		  <input type="hidden" name="DESTICAO" value="{{ $simbrief->xml->destination->icao_code }}" />
            		  <input type="hidden" name="EET" value="@php secstohhmm($simbrief->xml->times->est_time_enroute) @endphp" />
            		  <input type="hidden" name="ALTICAO" value="{{ $simbrief->xml->alternate->icao_code}}" />
            		  <input type="hidden" name="ALTICAO2" value="{{ $simbrief->xml->alternate2->icao_code}}" />
            		  <input type="hidden" name="OTHER" value="{{ $simbrief->xml->atc->section18 }}" />
            		  <input type="hidden" name="ENDURANCE" value="@php secstohhmm($simbrief->xml->times->endurance) @endphp" />
            		  <input type="hidden" name="POB" value="{{ $simbrief->xml->weights->pax_count }}" />
            		  <input id="ivao_prefile" type="submit" class="btn btn-primary" value="File ATC on IVAO" />
				    </form>	
             </div>
             <div class="col-4" align="center">
				      <form action="https://my.vatsim.net/pilots/flightplan" method="GET" target="_blank">
					        <input type="hidden" name="raw" value="{{ $simbrief->xml->atc->flightplan_text }}">
					        <input type="hidden" name="fuel_time" value="@php secstohhmm($simbrief->xml->times->endurance) @endphp">
					        <input type="hidden" name="speed" value="{{ $simbrief->xml->atc->initial_spd }}">
					        <input type="hidden" name="altitude" value="{{ $simbrief->xml->atc->initial_alt }}">
					        <input id="vatsim_prefile" type="submit" class="btn btn-primary" value="File ATC on VATSIM"/>
				        </form>
            </div>
			<div class="col-4" align="center">
				<a href="http://skyvector.com/?chart=304&amp;fpl={{ $simbrief->xml->origin->icao_code}} {{ $simbrief->xml->general->route }} {{ $simbrief->xml->destination->icao_code}}" target="_blank" class="btn btn-info">View Route At SkyVector</a>
			</div>
             </div>
           </div>
          </div>          
          <div class="form-container">
            <h6><i class="fas fa-info-circle"></i>
              &nbsp;OFP
            </h6>
            <div class="form-container-body border border-dark">
              <div class="overflow-auto" style="height: 750px;">
                {!! $simbrief->xml->text->plan_html !!}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="form-container">
            <h6><i class="fas fa-info-circle"></i>
              &nbsp;Flight Maps
            </h6>
            <div class="form-container-body">
              @foreach($simbrief->images->chunk(2) as $images)
                <div class="row">
                  @foreach($images as $image)
                    <div class="col-6 text-center">
                      <p>
                        <img src="{{ $image['url'] }}" alt="{{ $image['name'] }}"/>
                        <small class="text-muted">{{ $image['name'] }}</small>
                      </p>
                    </div>
                  @endforeach
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <div class="row">
    <div class="col-12">
      @if (empty($simbrief->pirep_id))
        <a class="btn btn-outline-info pull-right"
           style="margin-top: -10px;margin-bottom: 5px"
           href="{{ url(route('frontend.simbrief.prefile', [$simbrief->id])) }}">Prefile PIREP</a>
      @endif
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    $(document).ready(function () {
      $("#download_fms").click(e => {
        e.preventDefault();
        const select = document.getElementById("download_fms_select");
        const link = select.options[select.selectedIndex].value;
        console.log('Downloading FMS: ', link);
        window.open(link, '_blank');
      });
    });
  </script>
@endsection

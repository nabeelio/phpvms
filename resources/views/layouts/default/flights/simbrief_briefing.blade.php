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
            <h6><i class="fas fa-info-circle"></i>
              &nbsp;Weather
            </h6>
            <div class="form-container-body">
              <div class="row">
                <div class="col-12">
                  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Departure METAR</p>
                    <p class="border border-dark rounded p-1 small text-monospace">
                      {{ $simbrief->xml->weather->orig_metar }}</p>
                  </div>

                  <hr/>

                  <div><p class="small text-uppercase pb-sm-0 mb-sm-1">Arrival METAR</p>
                    <p class="border border-dark rounded p-1 small text-monospace">
                      {{  $simbrief->xml->weather->dest_metar }}</p>
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
            <h6><i class="fas fa-info-circle"></i>
              &nbsp;OFP
            </h6>
            <div class="form-container-body border border-dark">
              <div class="overflow-auto" style="height: 600px;">
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

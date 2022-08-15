<div class="row">
  <div class="form-group col-sm-4">
    <div class="box box-solid">
      <div class="box-header with-border">
        {{--<i class="fa fa-text-width"></i>--}}
        <h3 class="box-title">{{ Form::label('dpt_airport_id', 'Dep ICAO') }}</h3>
      </div>
      <div class="box-body"><p class="lead">
          {{ $pirep->dpt_airport->icao }} - {{ $pirep->dpt_airport->name }}
        </p></div>
    </div>
  </div>

  <div class="form-group col-sm-5">
    <div class="box box-solid">
      <div class="box-header with-border">
        {{--<i class="fa fa-text-width"></i>--}}
        <h3 class="box-title">{{ Form::label('arr_airport_id', 'Arrival ICAO') }}</h3>
      </div>
      <div class="box-body"><p class="lead">
          {{ $pirep->arr_airport->icao }} - {{ $pirep->arr_airport->name }}
        </p>
      </div>
    </div>
  </div>

  <div class="form-group col-sm-3">
    <div id="pirep_{{ $pirep->id }}_actionbar" class="pull-right">
      @include('admin.pireps.actions')
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <table class="table table-hover">
      <tr>
        <td>Pilot</td>
        <td><a href="{{ route('admin.users.show', [$pirep->pilot->id]) }}"
               target="_blank">{{ $pirep->user->name }}</a>
        </td>
      </tr>
      <tr>
        <td>Flight</td>
        <td>
          <a href="{{ route('admin.flights.show', [$pirep->flight_id]) }}"
             target="_blank">
            {{ $pirep->ident }}
          </a>
        </td>
      </tr>
      <tr>
        <td>Aircraft</td>
        <td>{{ $pirep->aircraft->subfleet->name }}, {{ $pirep->aircraft->name }}
          ({{ $pirep->aircraft->registration }})
        </td>
      </tr>
      <tr>
        <td>Flight Time</td>
        <td>@minutestotime($pirep->flight_time)</td>
      </tr>
      <tr>
        <td>Flight Level</td>
        <td>{{ $pirep->level }}</td>
      </tr>
      <tr>
        <td>Distance</td>
        <td>{{ $pirep->distance }}</td>
      </tr>
      <tr>
        <td>Route</td>
        <td>{{ $pirep->route }}</td>
      </tr>
      <tr>
        <td>Notes</td>
        <td>{{ $pirep->notes }}</td>
      </tr>
      <tr>
        <td>Filed On</td>
        <td>{{ show_datetime($pirep->created_at) }}</td>
      </tr>

      <tr>
        <td>Updated On</td>
        <td>{{ show_datetime($pirep->updated_at) }}</td>
      </tr>
    </table>
  </div>
</div>

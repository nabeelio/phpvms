@extends('app')
@section('title', __('home.welcome.title'))

@section('content')      
        
        
     <div class="row">
    <div class="col-sm-6">
        <h2 class="description">Our fleet</h2>

        <table class="table table-hover table-responsive" id="aircrafts-table">
  <thead>
  <th>Name</th>
  <th style="text-align: center;">Registration</th>
  <th>Subfleet</th>
  <th style="text-align: center;">Location</th>
  <th style="text-align: center;">Hours</th>
  <th style="text-align: center;">Active</th>
  <th style="text-align: right;"></th>
  </thead>
  <tbody>
  @foreach($aircraft as $ac)
    <tr>
      <td>{{ $ac->name }}</td>
      <td style="text-align: center;">{{ $ac->registration }}</td>
      <td>
        @if($ac->subfleet_id && $ac->subfleet)
          
            {{ $ac->subfleet->name }}
          
        @else
          -
        @endif
      </td>
      <td style="text-align: center;">{{ $ac->airport_id }}</td>
      <td style="text-align: center;">
        @minutestotime($ac->flight_time)
      </td>
      <td style="text-align: center;">
        @if($ac->status == \App\Models\Enums\AircraftStatus::ACTIVE)
          <span class="label label-success">{{ \App\Models\Enums\AircraftStatus::label($ac->status) }}</span>
        @else
          <span class="label label-default">
                        {{ \App\Models\Enums\AircraftStatus::label($ac->status) }}
                    </span>
        @endif
      </td>
      
    </tr>
  @endforeach
  </tbody>
</table>

        
</div>
     
     <div class="col-sm-6">
     <h2 class="description">Our destinations</h2>
     <a href="Routes.pdf" title="Click for zoom"><img src="Routes.png"></a>
     </div>  
     </div>        
        

@endsection


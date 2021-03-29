@extends('app')
@section('title', __('common.fleet'))

@section('content')      
        
        
<div class="row">
  <div class="col-sm-12">
    <h2 class="description">@lang('common.fleet')</h2>
      <table class="table table-hover table-responsive" id="aircrafts-table">
        <thead>
            <th>@lang('common.name')</th>
            <th style="text-align: center;">@lang('common.livemap')</th>
            <th>@lang('common.subfleet')</th>
            <th style="text-align: center;">@lang('user.location')</th>
            <th style="text-align: center;">@lang('common.hour')</th>
            <th style="text-align: center;">@lang('common.active')</th>
            <th style="text-align: right;"></th>
        </thead>
        <tbody>
            @foreach($aircrafts as $ac)
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
     
   
</div>        
@endsection


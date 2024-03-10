<table class="w-full my-0 align-middle text-dark border-neutral-200">
  <thead class="align-bottom">
    <tr class="font-semibold text-[0.95rem] text-secondary-dark">
      <th class="pb-3 text-start min-w-[100px]">FLIGHT NUMBER</th>
      <th class="pb-3 text-start min-w-[175px]">DEPARTURE AIRPORT</th>
      <th class="pb-3 text-start min-w-[175px]">ARRIVAL AIRPORT</th>
      <th class="pb-3 pr-12 text-start min-w-[100px]">STATUS</th>
      <th class="pb-3 text-start min-w-[50px]">DETAILS</th>
    </tr>
  </thead>
  <tbody>
    <tr class="border-b border-dashed last:border-b-0">
      <td>
        <div class="flex items-center">
          <div class="flex flex-col justify-start">
            <a href="javascript:void(0)" class="font-semibold transition-colors duration-200 ease-in-out text-md/normal text-secondary-inverse hover:text-primary">{{ $pirep->ident }}</a>
          </div>
        </div>
      </td>
      <td>
        <span class="font-semibold text-light-inverse text-md/normal">{{ $pirep->dpt_airport->name }}</span>
        <span class="text-light-inverse text-md/normal">{{ $pirep->dpt_airport->icao }}</span>
      </td>
      <td>
        <span class="font-semibold text-light-inverse text-md/normal">{{ $pirep->arr_airport->name }}</span>
        <span class="text-light-inverse text-md/normal">{{ $pirep->arr_airport->icao }}</span>
      </td>
      <td>
        @if($pirep->state === PirepState::ACCEPTED)
          <span class="bg-green-100 text-green-800 text-md/normal font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-700">Accepted</span>
        @elseif($pirep->state === PirepState::REJECTED)
          <span class="bg-green-100 text-green-800 text-md/normal font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-700">Rejected</span>
        @elseif($pirep->state === PirepState::IN_PROGRESS)
          <span class="bg-green-100 text-green-800 text-md/normal font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-700">In Progress</span>
        @elseif($pirep->state === PirepState::PENDING)
          <span class="bg-green-100 text-green-800 text-md/normal font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-200 dark:text-yellow-700">Pending</span>
        @elseif($pirep->state === PirepState::CANCELLED)
          <span class="bg-green-100 text-green-800 text-md/normal font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-700">Cancelled</span>
        @elseif($pirep->state === PirepState::PAUSED)
          <span class="bg-green-100 text-green-800 text-md/normal font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-700">Paused</span>
        @endif
      </td>
      <td>
        <a class="ml-auto relative text-secondary-dark bg-light-dark hover:text-primary flex items-center h-[25px] w-[25px] text-base font-medium leading-normal text-center align-middle cursor-pointer rounded-2xl transition-colors duration-200 ease-in-out shadow-none border-0 justify-center" href="{{ route('frontend.pireps.show', [$pirep->id]) }}">
          <span class="flex items-center justify-center p-0 m-0 leading-none shrink-0 ">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
          </span>
        </a>
      </td>
    </tr>
  </tbody>
</table>


<!--
<div class="card-body" style="min-height: 0px">
  <div class="row">
    <div class="col-sm-10">
      <p>
        <a href="{{ route('frontend.pireps.show', [$pirep->id]) }}"></a>
        -
        {{ $pirep->dpt_airport->name }}
        (<a href="{{route('frontend.airports.show', [
                          'id' => $pirep->dpt_airport->icao
                          ])}}">{{$pirep->dpt_airport->icao}}</a>)
        <span class="description">to</span>
        {{ $pirep->arr_airport->name }}
        (<a href="{{route('frontend.airports.show', [
                          'id' => $pirep->arr_airport->icao
                          ])}}">{{$pirep->arr_airport->icao}}</a>)
      </p>
    </div>
    <div class="col-sm-2 float-right">
      <div class="col-sm-2 text-center">
          @if($pirep->state === PirepState::PENDING)
            <div class="badge badge-warning">
          @elseif($pirep->state === PirepState::ACCEPTED)
              <div class="badge badge-success">
          @elseif($pirep->state === PirepState::REJECTED)
              <div class="badge badge-danger">
          @else
             <div class="badge badge-info">
          @endif
            {{ PirepState::label($pirep->state) }}</div>
          <a href="{{ route('frontend.pireps.edit', [$pirep->id]) }}"
            class="btn btn-sm btn-info">@lang('common.edit')</a> 
      </div>    
    </div>
  </div>
</div>
        -->
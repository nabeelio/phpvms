<div class="flex flex-col p-4">
  <form action="{{ route('frontend.flights.search') }}" method="get">
    <div>
      <label for="flight_number" class="block text-sm font-medium leading-6 text-gray-900">Flight Number</label>
      <input id="flight_number" name="flight_number" class="block w-full rounded-md border-0 p-1 mt-1 text-gray-800 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-blue-900 sm:text-sm sm:leading-6">
    </div>
    
    <div class="mt-4">
      <label for="dep_icao" class="block text-sm font-medium leading-6 text-gray-900">Departure Airport</label>
      <select id="dep_icao" name="dep_icao" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-900 sm:max-w-xs sm:text-sm sm:leading-6">
        @foreach($airports as $icao => $airport)
          <option value="{{ $icao }}">{{ $airport }}</option>
        @endforeach
      </select>
    </div>

    <div class="mt-4">
      <label for="arr_icao" class="block text-sm font-medium leading-6 text-gray-900">Arrival Airport</label>
      <select id="arr_icao" name="arr_icao" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-900 sm:max-w-xs sm:text-sm sm:leading-6">
        @foreach($airports as $icao => $airport)
          <option value="{{ $icao }}">{{ $airport }}</option>
        @endforeach
      </select>
    </div>

    <div class="mt-4">
      <label for="subfleet_id" class="block text-sm font-medium leading-6 text-gray-900">Aircraft</label>
      <select id="subfleet_id" name="subfleet_id" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-900 sm:max-w-xs sm:text-sm sm:leading-6">
        @foreach($subfleets as $key => $aircraft)
          <option value="{{ $key }}">{{ $aircraft }}</option>
        @endforeach
      </select>
    </div>

    <div class="mt-4">
      <button type="submit" class="py-2 px-3 bg-blue-900 text-white">Submit</button>
    </div>

    
  </form>
</div>
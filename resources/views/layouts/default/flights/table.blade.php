@foreach($flights as $flight)
<div class="card border-blue-bottom">
    <div class="card-block" style="min-height: 0">
        <div class="row">
            <div class="col-sm-9">
                <h5>
                    <a class="text-c" href="{{ route('frontend.flights.show', [$flight->id]) }}">
                        {{ $flight->ident }}
                    </a>
                </h5>
            </div>
            <div class="col-sm-3 text-right">
                {{-- NOTE:
                     Don't remove the "save_flight" class, or the x-id attribute.
                     It will break the AJAX to save/delete

                     "x-saved-class" is the class to add/remove if the bid exists or not
                     If you change it, remember to change it in the in-array line as well
                --}}
                <button class="btn btn-round btn-icon btn-icon-mini
                               {{ in_array($flight->id, $saved, true) ? 'btn-info':'' }}
                               save_flight"
                        x-id="{{ $flight->id }}"
                        x-saved-class="btn-info"
                        type="button"
                        title="Add/Remove Bid"
                >
                    <i class="fas fa-map-marker"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-5">
                {{--<table class="table-condensed"></table>--}}
                <span class="title">DEP&nbsp;</span>
                {{ $flight->dpt_airport->name }}
                (<a href="{{route('frontend.airports.show', [
                            'id' => $flight->dpt_airport->icao
                            ])}}">{{$flight->dpt_airport->icao}}</a>)
                @if($flight->dpt_time), {{ $flight->dpt_time }}@endif
                <br />
                <span class="title">ARR&nbsp;</span>
                {{ $flight->arr_airport->name }}
                (<a href="{{route('frontend.airports.show', [
                            'id' => $flight->arr_airport->icao
                            ])}}">{{$flight->arr_airport->icao}}</a>)
                @if($flight->arr_time), {{ $flight->arr_time }}@endif
                <br />
                @if($flight->distance)
                    <span class="title">DISTANCE&nbsp;</span>
                    {{ $flight->distance }} {{ setting('units.distance') }}
                @endif
                <br />
                @if($flight->level)
                    <span class="title">LEVEL&nbsp;</span>
                    {{ $flight->level }} {{ setting('units.altitude') }}
                @endif
            </div>
            <div class="col-sm-7">
                <div class="row">
                    <div class="col-sm-12">
                        <span class="title">ROUTE&nbsp;</span>
                        {{ $flight->route }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endforeach

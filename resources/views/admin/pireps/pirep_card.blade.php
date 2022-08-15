<div id="pirep_{{ $pirep->id }}_container" class="card border-blue-bottom pirep_card_container">
	<div class="card-block" style="min-height: 0px">
		<div class="row">
      <div class="col-sm-2 text-center">
      <h5><a class="text-c" href="{{ route('admin.pireps.edit', [$pirep->id]) }}">{{ $pirep->ident }}</a></h5>
        <div id="pirep_{{ $pirep->id }}_status_container">
				@php 
				$PirepStateClass = "badge badge-info" ; 
        if($pirep->state === PirepState::PENDING ) { $PirepStateClass = "badge badge-warning" ; }
				if($pirep->state === PirepState::ACCEPTED ) { $PirepStateClass = "badge badge-success" ; }
				if($pirep->state === PirepState::REJECTED ) { $PirepStateClass = "badge badge-danger" ; }
				@endphp
          <div class="{{ $PirepStateClass }}">{{ PirepState::label($pirep->state) }}</div>
        </div>
			</div>
			<div class="col-sm-10">
				<div class="row">
					<div class="col-sm-6">
						<div>
							<span class="description">
								<b>Pilot</b>&nbsp;{{ '('.$pirep->user_id.') '.optional($pirep->user)->name }}
							</span>
						</div>
            <div>
							<span class="description">
								<b>DEP</b>&nbsp;{{ $pirep->dpt_airport_id }}&nbsp;
								<b>ARR</b>&nbsp;{{ $pirep->arr_airport_id }}&nbsp;
							</span>
						</div>
						<div>
							<span class="description">
								<b>Flight Time</b>&nbsp; @minutestotime($pirep->flight_time)
							</span>
						</div>
						@if($pirep->aircraft)
						<div>
							<span class="description">
								<b>Aircraft</b>&nbsp;{{ $pirep->aircraft->registration }} @if($pirep->aircraft->registration != $pirep->aircraft->name) '{{ $pirep->aircraft->name }}' @endif
							</span>
						</div>
						@endif
						@if(filled($pirep->level) && $pirep->level > 10)
						<div>
							<span class="description">
								<b>Flight Level</b>&nbsp;{{ $pirep->level }}
							</span>
						</div>
						@endif
						<div>
							<span class="description">
								<b>Filed Using</b>&nbsp; {{ PirepSource::label($pirep->source) }}
								@if(filled($pirep->source_name)) ({{ $pirep->source_name }}) @endif
							</span>
						</div>
						<div>
							<span class="description">
								<b>File Date</b>&nbsp; {{ show_datetime($pirep->created_at) }}
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div id="pirep_{{ $pirep->id }}_actionbar" class="pull-right">
					@include('admin.pireps.actions', ['pirep' => $pirep, 'on_edit_page' => false])
				</div>
			</div>
		</div>
	</div>
</div>

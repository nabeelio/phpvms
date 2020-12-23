{{-- DEFINE YOUR TEXT HERE ---}}
@php
    $last = 'Last' ;
    $days = 'Days' ;
    $noreports = 'No Reports' ;
    $statname = 'Personal Stats Widget' ;
    if($type == 'avglanding') { $statname = 'Average Landing Rate' ; }
    if($type == 'avgscore') { $statname = 'Average Score' ; }
    if($type == 'avgdistance') { $statname = 'Average Distance' ; }
    if($type == 'totdistance') { $statname = 'Total Distance' ; }
    if($type == 'avgtime') { $statname = 'Average Flight Time' ; }
    if($type == 'tottime') { $statname = 'Total Flight Time' ; }
    if($type == 'avgfuel') { $statname = 'Average Fuel Burn' ; }
    if($type == 'totfuel') { $statname = 'Total Fuel Burn' ; }
@endphp
@if($disp == 'full')
<div class="card">
    <div class="card-body">
        <div class="statistic-details">
            <div class="statistic-details-item">
                <div class="detail-value">
                @if($pstat <> 0)
                    @if($type == 'avgtime' || $type == 'tottime')
                        @minutestotime($pstat)
                    @else
                        {{ $pstat }}
                    @endif
                @else
                    {{ $noreports }}
                @endif           
                </div>
                <div class="detail-name">{{ $statname }} @if($period)({{ $last }} {{ $period }} {{ $days }})@endif</div>
            </div>
        </div>
    </div>
</div>
@else
    @if($pstat <> 0)
        @if($type == 'avgtime' || $type == 'tottime')
            @minutestotime($pstat)
        @else
            {{ $pstat }}
        @endif
    @else
        {{ $noreports }}
    @endif  
@endif
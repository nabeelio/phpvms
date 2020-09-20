<div id="aircraft_fares_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  <div class="header">
    <h3>fares</h3>
    @component('admin.components.info')
      Fares assigned to the current subfleet. These can be overridden,
      otherwise, the value used is the default, which comes from the fare.
      The pay can be set as a fixed amount, or a percentage of the base fare
    @endcomponent
  </div>
  <br/>
  <table id="aircraft_fares" class="table table-hover">
    @if(count($subfleet->fares))
      <thead>
      <tr>
        <th>Name</th>
        <th style="text-align: center;">Code</th>
        <th style="text-align: center;">Type</th>
        <th style="text-align: center;">Capacity (default)</th>
        <th style="text-align: center;">Price (default)</th>
        <th style="text-align: center;">Cost (default)</th>
        <th></th>
      </tr>
      </thead>
    @endif
    <tbody>
    @foreach($subfleet->fares as $atf)
      <tr>
        <td class="sorting_1 text-center">{{ $atf->name }}</td>
        <td class="text-center">{{ $atf->code }}</td>
        <td class="sorting_1 text-center">{{ \App\Models\Enums\FareType::label($atf->type)  }}</td>
        <td class="text-center">
          <a href="#" data-pk="{{ $atf->id }}" data-name="capacity">{{ $atf->pivot->capacity }}</a>
          <span class="small background-color-grey-light">({{ $atf->capacity }})</span>
        </td>
        <td class="text-center">
          <a href="#" data-pk="{{ $atf->id }}" data-name="price">{{ $atf->pivot->price }}</a>
          <span class="small background-color-grey-light">({{ $atf->price }})</span></td>
        <td class="text-center">
          <a href="#" data-pk="{{ $atf->id }}" data-name="cost">{{ $atf->pivot->cost }}</a>
          <span class="small background-color-grey-light">({{ $atf->cost}})</span></td>
        <td style="text-align: right; width:3%;">
          {{ Form::open(['url' => '/admin/subfleets/'.$subfleet->id.'/fares',
                          'method' => 'delete',
                          'class' => 'rm_fare'
                          ])
          }}
          {{ Form::hidden('fare_id', $atf->id) }}
          {{ Form::button('<i class="fa fa-times"></i>',
                           ['type' => 'submit',
                            'class' => 'btn btn-sm btn-danger btn-icon']) }}
          {{ Form::close() }}
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <hr/>
  <div class="row">
    <div class="col-xs-12">
      <div class="text-right">
        {{ Form::open(['url' => '/admin/subfleets/'.$subfleet->id.'/fares',
                        'method' => 'post',
                        'class' => 'rm_fare form-inline'
                        ])
        }}
        {{ Form::select('fare_id', $avail_fares, null, [
                'placeholder' => 'Select Fare',
                'class' => 'ac-fare-dropdown form-control input-lg select2',
            ])
        }}
        {{ Form::button('<i class="glyphicon glyphicon-plus"></i> add',
                         ['type' => 'submit',
                          'class' => 'btn btn-success btn-s']) }}
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

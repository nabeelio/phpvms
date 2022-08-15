<div id="flight_fares_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  <div class="header">
    <h3>fares</h3>
    @component('admin.components.info')
      Fares assigned to the current flight. These can be overridden, otherwise,
      the values used come from the subfleet of the aircraft that the flight is
      filed with. Only assign the fares you want to override. They can be set as
      a monetary amount, or a percentage.
      <a href="https://docs.phpvms.net/guides/finances" target="_blank">Read documentation about finances</a>.
    @endcomponent

    <p class="text-danger">{{ $errors->first('value') }}</p>
  </div>

  @if(count($flight->fares) === 0)
    @include('admin.common.none_added', ['type' => 'fares'])
  @endif

  <table id="flight_fares"
         class="table table-hover"
         role="grid" aria-describedby="aircraft_fares_info">
    @if(count($flight->fares))
      <thead>
      <tr role="row">
        <th>Name</th>
        <th style="text-align: center;">Code</th>
        <th>Capacity</th>
        <th>Price</th>
        <th>Cost</th>
        <th>Actions</th>
      </tr>
      </thead>
    @endif
    <tbody>
    @foreach($flight->fares as $atf)
      <tr>
        <td class="sorting_1">{{ $atf->name }}</td>
        <td style="text-align: center;">{{ $atf->code }}</td>
        <td>
          <a href="#" data-pk="{{ $atf->id }}" data-name="capacity">{{ $atf->pivot->capacity }}</a>
          <span class="small background-color-grey-light">({{ $atf->capacity }})</span>
        </td>
        <td>
          <a href="#" data-pk="{{ $atf->id }}" data-name="price">{{ $atf->pivot->price }}</a>
          <span class="small background-color-grey-light">({{ $atf->price }})</span>
        </td>
        <td>
          <a href="#" data-pk="{{ $atf->id }}" data-name="cost">{{ $atf->pivot->cost }}</a>
          <span class="small background-color-grey-light">({{ $atf->cost }})</span>
        </td>
        <td style="text-align: center; width:3%;">
          {{ Form::open(['url' => '/admin/flights/'.$flight->id.'/fares',
                          'method' => 'delete',
                          'class' => 'pjax_fares_form'
                          ])
          }}
          {{ Form::hidden('fare_id', $atf->id) }}
          {{ Form::button('<i class="fa fa-times"></i>',
                           ['type' => 'submit',
                            'class' => 'btn btn-danger btn-xs']) }}
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
        {{ Form::open(['url' => '/admin/flights/'.$flight->id.'/fares',
                'method' => 'post',
                'class' => 'pjax_fares_form form-inline'
        ]) }}
        {{ Form::select('fare_id', $avail_fares, null, [
                'placeholder' => 'Select Fare',
                'class' => 'ac-fare-dropdown form-control input-lg select2',
                'style' => 'width: 400px;',
        ]) }}
        {{ Form::button('<i class="glyphicon glyphicon-plus"></i> add', [
                'type' => 'submit',
                'class' => 'btn btn-success btn-s']
        ) }}
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

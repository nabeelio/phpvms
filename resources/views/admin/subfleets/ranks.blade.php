<div id="subfleet_ranks_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  <div class="header">
    <h3>ranks</h3>
    @component('admin.components.info')
      These ranks are allowed to fly aircraft in this subfleet. The pay can be
      set as a fixed amount, or a percentage of the rank's base payrate
    @endcomponent
  </div>
  <br/>
  <table id="subfleet_ranks" class="table table-hover">
    @if(count($subfleet->ranks))
      <thead>
      <tr>
        <th>Name</th>
        <th style="text-align: center;">Base rate</th>
        <th style="text-align: center;">ACARS pay</th>
        <th style="text-align: center;">Manual pay</th>
        <th></th>
      </tr>
      </thead>
    @endif
    <tbody>
    @foreach($subfleet->ranks as $rank)
      <tr>
        <td class="sorting_1">{{ $rank->name }}</td>
        <td style="text-align: center;">{{ $rank->base_pay_rate ?: '-' }}</td>
        <td style="text-align: center;">
          <a href="#" data-pk="{{ $rank->id }}"
             data-name="acars_pay">{{ $rank->pivot->acars_pay }}</a>
        </td>

        <td style="text-align: center;">
          <a href="#" data-pk="{{ $rank->id }}"
             data-name="manual_pay">{{ $rank->pivot->manual_pay }}</a>
        </td>

        <td style="text-align: right; width:3%;">
          {{ Form::open(['url' => '/admin/subfleets/'.$subfleet->id.'/ranks',
                          'method' => 'delete',
                          'class' => 'modify_rank'])
          }}
          {{ Form::hidden('rank_id', $rank->id) }}
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
        {{ Form::open(['url' => '/admin/subfleets/'.$subfleet->id.'/ranks',
                        'method' => 'post',
                        'class' => 'modify_rank form-inline'])
        }}
        {{ Form::select('rank_id', $avail_ranks, null, [
                'placeholder' => 'Select Rank',
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
{{--</div></div>--}}

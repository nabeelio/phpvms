<table class="table table-hover table-responsive" id="expenses-table">
  <thead>
  <th>Name</th>
  <th>Type</th>
  <th style="text-align: center;">Amount</th>
  <th>Airline</th>
  <th class="text-center">Active</th>
  <th></th>
  </thead>
  <tbody>
  @foreach($expenses as $expense)
    <tr>
      <td><a href="{{ route('admin.expenses.edit', [$expense->id]) }}">
          {{ $expense->name }}</a>
      </td>
      <td>{{ \App\Models\Enums\ExpenseType::label($expense->type) }}</td>
      <td style="text-align: center;">{{ $expense->amount }}</td>
      <td>
        @if(filled($expense->airline))
          {{ $expense->airline->name }}
        @else
          <span class="description">-</span>
        @endif
      </td>
      <td class="text-center">
                <span class="label label-{{ $expense->active?'success':'default' }}">
                    {{ \App\Models\Enums\ActiveState::label($expense->active) }}
                </span>
      </td>
      <td class="text-right">
        {{ Form::open(['route' => ['admin.expenses.destroy', $expense->id], 'method' => 'delete']) }}
        <a href="{{ route('admin.expenses.edit', [$expense->id]) }}"
           class='btn btn-sm btn-success btn-icon'><i class="fas fa-pencil-alt"></i></a>
        {{ Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure?')"]) }}
        {{ Form::close() }}
      </td>
    </tr>
  @endforeach
  </tbody>
</table>

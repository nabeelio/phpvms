<div class="content table-responsive table-full-width">

    <div class="header">
        @component('admin.components.info')
            Flights fields that can be filled out. You can still add other custom fields
            directly in the flight, but this provides a template for all flights.
        @endcomponent
    </div>

    <table class="table table-hover table-responsive" id="pirepFields-table">
    <thead>
        <th>Name</th>
        <th></th>
    </thead>
    <tbody>
    @foreach($fields as $field)
        <tr>
            <td>{{ $field->name }}</td>
            <td class="text-right">
                {{ Form::open(['route' => ['admin.flightfields.destroy', $field->id], 'method' => 'delete']) }}
                <a href="{{ route('admin.flightfields.edit', [$field->id]) }}"
                   class='btn btn-sm btn-success btn-icon'>
                    <i class="fas fa-pencil-alt"></i></a>

                {{ Form::button('<i class="fa fa-times"></i>',
                             ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon',
                              'onclick' => "return confirm('Are you sure?')"]) }}
                {{ Form::close() }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>

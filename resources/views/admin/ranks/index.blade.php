@extends('admin.app')
@section('title', 'Ranks')
@section('actions')
    <li>
        <a href="{!! route('admin.ranks.create') !!}">
            <i class="ti-plus"></i>
            Add New</a>
    </li>
@endsection

@section('content')
    <div class="card">
        @include('admin.ranks.table')
    </div>
@endsection
@section('scripts')
<script type="text/javascript">
$(document).ready(function () {
    $(document).on('submit', 'form.add_rank', function (event) {
        event.preventDefault();
        $.pjax.submit(event, '#ranks_table_wrapper', {push: false});
    });
});
</script>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $(".select2").select2();

    $(document).on('submit', 'form.add_rank', function (event) {
        event.preventDefault();
        $.pjax.submit(event, '#ranks_table_wrapper', {push: false});
    });

    $(document).on('submit', 'form.pjax_form', function (event) {
        event.preventDefault();
        $.pjax.submit(event, '#rank_subfleet_wrapper', {push: false});
    });

    $(document).on('pjax:complete', function () {
        $(".select2").select2();
    });
});
</script>
@endsection

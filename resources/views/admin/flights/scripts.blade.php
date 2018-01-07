@section('scripts')
<script>
    $(document).ready(function () {

        $('#flight_fields_wrapper a.inline').editable({
            type: 'text',
            mode: 'inline',
            emptytext: '0',
            url: '/admin/flights/{!! $flight->id !!}/fields',
            ajaxOptions: {'type': 'put'},
            params: function (params) {
                return {
                    field_id: params.pk,
                    name: params.name,
                    value: params.value
                }
            }
        });

        $(document).on('submit', 'form.pjax_flight_fields', function (event) {
            event.preventDefault();
            $.pjax.submit(event, '#flight_fields_wrapper', {push: false});
        });

        $(document).on('submit', 'form.pjax_subfleet_form', function (event) {
            event.preventDefault();
            $.pjax.submit(event, '#subfleet_flight_wrapper', {push: false});
        });

        $(document).on('pjax:complete', function () {
            $(".select2").select2();
        });
    });
</script>
@endsection

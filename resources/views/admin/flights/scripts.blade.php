@section('scripts')
<script>

function setEditable() {
    $('#flight_fares a').editable({
        type: 'text',
        mode: 'inline',
        emptytext: 'inherited',
        url: '{{ url('/admin/flights/'.$flight->id.'/fares') }}',
        title: 'Enter override value',
        ajaxOptions: {'type': 'put'},
        params: function (params) {
            return {
                fare_id: params.pk,
                name: params.name,
                value: params.value
            }
        }
    });
}

$(document).ready(function () {

    setEditable();

    $('#flight_fields_wrapper a.inline').editable({
        type: 'text',
        mode: 'inline',
        emptytext: '0',
        url: '/admin/flights/{{ $flight->id }}/fields',
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

    $(document).on('submit', 'form.pjax_fares_form', function (event) {
        event.preventDefault();
        console.log(event);
        $.pjax.submit(event, '#flight_fares_wrapper', {push: false});
        setEditable();
    });

    $(document).on('pjax:complete', function () {
        $(".select2").select2();
        setEditable();
    });
});
</script>
@endsection

@section('scripts')
<script>

const setEditable = () =>
{
    const api_key = $('meta[name="api-key"]').attr('content');
    const csrf_token = $('meta[name="csrf-token"]').attr('content');

    $('#flight_fares a').editable({
        type: 'text',
        mode: 'inline',
        emptytext: 'inherited',
        url: '{{ url('/admin/flights/'.$flight->id.'/fares') }}',
        title: 'Enter override value',
        ajaxOptions: {
            type: 'post',
            headers: {
                'x-api-key': api_key,
                'X-CSRF-TOKEN': csrf_token
            }
        },
        params: function (params) {
            return {
                _method: 'put',
                fare_id: params.pk,
                name: params.name,
                value: params.value
            }
        }
    });
};

const setFieldsEditable = () =>
{
    const api_key = $('meta[name="api-key"]').attr('content');
    const csrf_token = $('meta[name="csrf-token"]').attr('content');

    $('#flight_fields_wrapper a.inline').editable({
        type: 'text',
        mode: 'inline',
        emptytext: 'no value',
        url: '{{ url('/admin/flights/'.$flight->id.'/fields') }}',
        ajaxOptions: {
            type: 'post',
            headers: {
                'x-api-key': api_key,
                'X-CSRF-TOKEN': csrf_token
            }
        },
        params: function (params) {
            return {
                _method: 'put',
                field_id: params.pk,
                name: params.name,
                value: params.value
            }
        }
    });
};

$(document).ready(function () {

    setEditable();
    setFieldsEditable();

    /*new Pjax({
        elements: 'form[action].pjax_subfleet_form',
        selectors: ['div#subfleet_flight_wrapper'],
        history: false,
    });

    new Pjax({
        elements: 'form[action].pjax_flight_fields',
        selectors: ['div#flight_fields_wrapper'],
        history: false
    });*/

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
        $.pjax.submit(event, '#flight_fares_wrapper', {push: false});
    });

    $(document).on('pjax:complete', function () {
        initPlugins();
        setEditable();
        setFieldsEditable();
    });
});
</script>
@endsection

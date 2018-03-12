@section('scripts')
<script>
function setEditable() {
    @if(isset($airport))
    $('#airport-expenses a.text').editable({
        emptytext: '0',
        url: '{{ url('/admin/airports/'.$airport->id.'/expenses') }}',
        title: 'Enter override value',
        ajaxOptions: {'type': 'put'},
        params: function (params) {
            return {
                expense_id: params.pk,
                name: params.name,
                value: params.value
            }
        }
    });

    $('#airport-expenses a.dropdown').editable({
        type: 'select',
        emptytext: '0',
        source: {{ json_encode(list_to_editable(\App\Models\Enums\ExpenseType::select())) }},
        url: '{{ url('/admin/airports/'.$airport->id.'/expenses') }}',
        title: 'Enter override value',
        ajaxOptions: {'type': 'put'},
        params: function (params) {
            return {
                expense_id: params.pk,
                name: params.name,
                value: params.value
            }
        }
    });
    @endif
}

function phpvms_vacentral_airport_lookup(icao, callback) {
    $.ajax({
        url: BASE_URL + '/api/airports/'+ icao + '/lookup',
        method: 'GET',
        headers: {
            'x-api-key': PHPVMS_USER_API_KEY
        }
    }).done(function (data, status) {
        callback(data.data);
    });
}

$(document).ready(function() {

    setEditable();

    $('#airports-table a.inline').editable({
        type: 'text',
        mode: 'inline',
        emptytext: '0',
        url: '{{ url('/admin/airports/fuel') }}',
        title: 'Enter price per unit of fuel',
        ajaxOptions: {'type': 'put'},
        params: function(params) {
            return {
                id: params.pk,
                name: params.name,
                value: params.value
            }
        }
    });

    $('a.airport_data_lookup').click(function(e) {
        e.preventDefault();
        const icao = $("input#airport_icao").val();
        if(icao === '') {
            return;
        }

        phpvms_vacentral_airport_lookup(icao, function(data) {
            console.log('lookup data', data);
            _.forEach(data, function(value, key) {
                if(key === 'city') {
                    key = 'location';
                }

                $("#" + key).val(value);

                if(key === 'tz') {
                    $("#timezone").val(value);
                    $("#timezone").trigger('change');
                }
            });
        });
    });

    $(document).on('submit', 'form.modify_expense', function (event) {
        event.preventDefault();
        console.log(event);
        $.pjax.submit(event, '#airport-expenses-wrapper', {push: false});
    });

    $(document).on('pjax:complete', function () {
        $(".select2").select2();
        setEditable();
    });
});
</script>
@endsection

@section('scripts')
<script>

$(document).ready(function() {

    $('#airports-table a.inline').editable({
        type: 'text',
        mode: 'inline',
        emptytext: '0',
        url: '/admin/airports/fuel',
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
        var icao = $("input#airport_icao").val();
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
                    $("#tz").trigger('change');
                }
            });
        });
    });
});
</script>
@endsection

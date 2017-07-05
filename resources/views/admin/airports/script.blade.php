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
});
</script>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $(".ac-fare-dropdown").select2();
    $('#aircraft_fares a').editable({
        type: 'text',
        mode: 'inline',
        emptytext: 'default',
        url: '/admin/subfleets/{!! $subfleet->id !!}/fares',
        title: 'Enter override value',
        ajaxOptions: { 'type': 'put'},
        params: function(params) {
            return {
                fare_id: params.pk,
                name: params.name,
                value: params.value
            }
        }
    });

    $(document).on('submit', 'form.rm_fare', function(event) {
        event.preventDefault();
        $.pjax.submit(event, '#aircraft_fares_wrapper', {push: false});
    });

    $(document).on('pjax:complete', function() {
        $(".select2").select2();
    });
});
</script>
@endsection

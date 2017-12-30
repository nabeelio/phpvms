@section('scripts')
<script>
function setEditable() {
    $('#aircraft_fares a').editable({
        type: 'text',
        mode: 'inline',
        emptytext: 'default',
        url: '/admin/subfleets/{!! $subfleet->id !!}/fares',
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

$(document).ready(function() {

    setEditable();

    $(document).on('submit', 'form.rm_fare', function(event) {
        event.preventDefault();
        console.log(event);
        $.pjax.submit(event, '#aircraft_fares_wrapper', {push: false});
        setEditable();
    });

    $(document).on('pjax:complete', function() {
        $(".select2").select2();
        setEditable();
    });
});
</script>
@endsection

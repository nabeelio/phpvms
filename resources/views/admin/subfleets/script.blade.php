@section('scripts')
<script>
function setEditable() {
    $('#aircraft_fares a').editable({
        type: 'text',
        mode: 'inline',
        emptytext: 'inherited',
        url: '{!! url('/admin/subfleets/'.$subfleet->id.'/fares') !!}',
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

    $('#subfleet_ranks a').editable({
        type: 'text',
        mode: 'inline',
        emptytext: 'inherited',
        url: '{!! url('/admin/subfleets/'.$subfleet->id.'/ranks') !!}',
        title: 'Enter override value',
        ajaxOptions: {'type': 'put'},
        params: function (params) {
            return {
                rank_id: params.pk,
                name: params.name,
                value: params.value
            }
        }
    });

    $('#subfleet-expenses a').editable({
        type: 'text',
        mode: 'inline',
        emptytext: '0',
        url: '{!! url('/admin/subfleets/'.$subfleet->id.'/expenses') !!}',
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
}

$(document).ready(function() {

    setEditable();

    $(document).on('submit', 'form.rm_fare', function(event) {
        event.preventDefault();
        console.log(event);
        $.pjax.submit(event, '#aircraft_fares_wrapper', {push: false});
        setEditable();
    });

    $(document).on('submit', 'form.modify_rank', function (event) {
        event.preventDefault();
        console.log(event);
        $.pjax.submit(event, '#subfleet_ranks_wrapper', {push: false});
    });

    $(document).on('submit', 'form.modify_expense', function (event) {
        event.preventDefault();
        console.log(event);
        $.pjax.submit(event, '#subfleet-expenses-wrapper', {push: false});
    });

    $(document).on('pjax:complete', function() {
        $(".select2").select2();
        setEditable();
    });
});
</script>
@endsection

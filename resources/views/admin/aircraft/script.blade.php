@section('scripts')
<script>
function setEditable() {
    @if(isset($aircraft))
    $('#expenses a.text').editable({
        emptytext: '0',
        url: '{{ url('/admin/aircraft/'.$aircraft->id.'/expenses') }}',
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

    $('#expenses a.dropdown').editable({
        type: 'select',
        emptytext: '0',
        source: {{ json_encode(list_to_editable(\App\Models\Enums\ExpenseType::select())) }},
        url: '{{ url('/admin/aircraft/'.$aircraft->id.'/expenses') }}',
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

$(document).ready(function() {

    setEditable();

    $(document).on('submit', 'form.modify_expense', function (event) {
        event.preventDefault();
        console.log(event);
        $.pjax.submit(event, '#expenses-wrapper', {push: false});
    });

    $(document).on('pjax:complete', function () {
        $(".select2").select2();
        setEditable();
    });
});
</script>
@endsection

@section('scripts')
<script>
function changeStatus(values) {
    const destContainer = '#pirep_' + values.pirep_id + '_actionbar';
    $.ajax({
        url: BASE_URL + '/admin/pireps/' + values.pirep_id + '/status',
        data: values,
        type: 'POST',
        headers: {
            'x-api-key': PHPVMS_USER_API_KEY
        },
        success: function (data) {
            // console.log(data);
            $(destContainer).html(data);
        }
    });
}

$(document).ready(function() {

    $(document).on('submit', 'form.pjax_form', function (event) {
        event.preventDefault();
        $.pjax.submit(event, '#pirep_comments_wrapper', {push: false});
    });

    $(document).on('pjax:complete', function () {
        $(".select2").select2();
    });

    $(document).on('submit', 'form.pirep_submit_status', function (event) {
        console.log(event);

        event.preventDefault();
        const values = {
            pirep_id: $(this).attr('pirep_id'),
            new_status: $(this).attr('new_status')
        };

        console.log(values);
        console.log('Changing PIREP ' + values.pirep_id + ' to state ' + values.new_status);

        changeStatus(values);
    });
});
</script>
@endsection

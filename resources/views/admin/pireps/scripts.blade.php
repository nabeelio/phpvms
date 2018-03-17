@section('scripts')
<script>
const changeStatus = (values, fn) => {
    console.log('Changing PIREP ' + values.pirep_id + ' to state ' + values.new_status);
    $.ajax({
        url: BASE_URL + '/admin/pireps/' + values.pirep_id + '/status',
        data: values,
        type: 'POST',
        headers: {
            'x-api-key': PHPVMS_USER_API_KEY
        },
        success: function (data) {
            fn(data);
        }
    });
};

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

        changeStatus(values, (data) => {
            const destContainer = '#pirep_' + values.pirep_id + '_actionbar';
            $(destContainer).html(data);
        });
    });

    $(document).on('submit', 'form.pirep_change_status', function(event) {
        event.preventDefault();
        changeStatus({
            pirep_id: $(this).attr('pirep_id'),
            new_status: $(this).attr('new_status')
        }, (data) => {
            location.reload();
        });
    });

});
</script>
@endsection

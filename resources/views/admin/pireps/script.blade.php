@section('scripts')
<script>
function changeStatus(values) {
    var destContainer = '#pirep_' + values.pirep_id + '_container';
    $.ajax({
        url: BASE_URL + '/admin/pireps/' + values.pirep_id + '/status',
        data: values,
        type: 'POST',
        headers: {
            'x-api-key': PHPVMS_USER_API_KEY
        },
        success: function (data) {
            // console.log(data);
            $(destContainer).replaceWith(data);
        }
    });
}

$(document).ready(function() {
    $(document).on('submit', 'form.pirep_submit_status', function (event) {
        console.log(event);

        event.preventDefault();
        var values = {
            pirep_id: $(this).attr('pirep_id'),
            new_status: $(this).attr('new_status')
        };

        console.log(values);
        console.log('Changing PIREP ' + values.pirep_id + ' to state ' + values.new_status);

        //var destContainer = '#pirep_' + pirep_id + '_container';
        //$.pjax.submit(event, destContainer, { push: false, maxCacheLength: 0 });

        changeStatus(values);
    });
});
</script>
@endsection

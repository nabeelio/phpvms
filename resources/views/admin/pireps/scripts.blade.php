@section('scripts')
<script>
const changeStatus = (values, fn) => {

    const api_key = $('meta[name="api-key"]').attr('content');
    const token = $('meta[name="csrf-token"]').attr('content');

    console.log('Changing PIREP ' + values.pirep_id + ' to state ' + values.new_status);

    $.ajax({
        url: '{{url('/admin/pireps')}}/' + values.pirep_id + '/status',
        data: values,
        type: 'POST',
        headers: {
            'x-api-key': api_key,
            'X-CSRF-TOKEN': token,
        },
        success: function (data) {
            fn(data);
        }
    });
};

$(document).ready(() => {

    const api_key = $('meta[name="api-key"]').attr('content');
    const token = $('meta[name="csrf-token"]').attr('content');

    const select_id = "select#aircraft_select";
    const destContainer = $('#fares_container');

    $(select_id).change((e) => {
        const aircraft_id = $(select_id + " option:selected").val();
        console.log('aircraft select change: ', aircraft_id);

        $.ajax({
            url: "{{ url('/admin/pireps/fares') }}?aircraft_id=" + aircraft_id,
            type: 'GET',
            headers: {
                'x-api-key': api_key,
                'X-CSRF-TOKEN': token,
            },
            success: (data) => {
                console.log('returned new fares', data);
                destContainer.html(data);
            },
            error: () => {
                destContainer.html('');
            }
        });
    });

    $(document).on('submit', 'form.pjax_form', (event) => {
        event.preventDefault();
        $.pjax.submit(event, '#pirep_comments_wrapper', {push: false});
    });

    $(document).on('pjax:complete', function () {
        $(".select2").select2();
    });

    /**
     * Recalculate finances button is clicked
     */
    $('button#recalculate-finances').on('click', (event) => {
        event.preventDefault();
        console.log('Sending recalculate finances request');
        const pirep_id = $(event.currentTarget).attr('data-pirep-id');

        $.ajax({
            url: '{{url('/api/pireps')}}/' + pirep_id + '/finances/recalculate',
            type: 'POST',
            headers: {
                'x-api-key': api_key,
                'X-CSRF-TOKEN': token,
            },
            success: (data) => {
                console.log(data);
                location.reload();
            }
        });
    });

    $(document).on('submit', 'form.pirep_submit_status', (event) => {
        event.preventDefault();
        const values = {
            pirep_id: $(event.currentTarget).attr('pirep_id'),
            new_status: $(event.currentTarget).attr('new_status')
        };

        console.log('change status', values);

        changeStatus(values, (data) => {
            const destContainer = '#pirep_' + values.pirep_id + '_actionbar';
            $(destContainer).html(data);
        });
    });

    $(document).on('submit', 'form.pirep_change_status', (event) => {
        event.preventDefault();

        const values = {
            pirep_id: $(event.currentTarget).attr('pirep_id'),
            new_status: $(event.currentTarget).attr('new_status')
        };

        console.log('change status', values);

        changeStatus(values, (data) => {
            location.reload();
        });
    });

});
</script>
@endsection

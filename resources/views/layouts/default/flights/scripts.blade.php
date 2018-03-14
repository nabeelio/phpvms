@section('scripts')
<script>
$(document).ready(function () {
    $("button.save_flight").click(function (e) {
        e.preventDefault();

        const btn = $(this);
        const class_name = btn.attr('x-saved-class'); // classname to use is set on the element

        let params = {
            url: '{{ url('/api/user/bids') }}',
            data: {
                'flight_id': btn.attr('x-id')
            }
        };

        if (btn.hasClass(class_name)) {
            params.method = 'DELETE';
        } else {
            params.method = 'POST';
        }

        axios(params).then(response => {
            console.log('save bid response', response);

            if(params.method === 'DELETE') {
                console.log('successfully removed flight');
                btn.removeClass(class_name);
                alert('Your bid was removed');
            } else {
                console.log('successfully saved flight');
                btn.addClass(class_name);
                alert('Your bid was added');
            }
        })
        .catch(error => {
            console.error('Error saving bid status', params, error);
        });
    });
});
</script>
@endsection

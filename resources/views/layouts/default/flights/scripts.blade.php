@section('scripts')
<script>
$(document).ready(function () {
    $("button.save_flight").click(function (e) {
        e.preventDefault();

        const btn = $(this);
        const class_name = btn.attr('x-saved-class');

        let params = {
            data: {
                'flight_id': btn.attr('x-id')
            },
            headers: {
                'x-api-key': "{!! Auth::user()->api_key !!}"
            }
        };

        if (btn.hasClass(class_name)) {
            params.method = 'DELETE';
            params.success = function () {
                console.log('successfully removed flight');
                btn.removeClass(class_name);
                alert('Your bid was removed');
            }
        } else {
            params.method = 'PUT';
            params.success = function () {
                console.log('successfully saved flight');
                btn.addClass(class_name);
                alert('Your bid was added');
            }
        }

        $.ajax('/api/user/bids', params);
    });
});
</script>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $("button.save_flight").click(async function (e) {
                e.preventDefault();

                const btn = $(this);
                const class_name = btn.attr('flight-saved-class'); // classname to use is set on the element
                const flight_id = btn.attr('flight-id');

                if (!btn.hasClass(class_name)) {
                    await phpvms.bids.addBid(flight_id);

                    console.log('successfully saved flight');
                    document.location.reload(true)
                } else {
                    await phpvms.bids.removeBid(flight_id);

                    console.log('successfully removed flight');
                    document.location.reload(true)
                }
            });
        });
    </script>
@endsection

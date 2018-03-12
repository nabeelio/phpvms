@section('scripts')
    <script>
        $(document).ready(() => {
            const select_id = "select#aircraft_select";
            const destContainer = $('#fares_container');

            $(select_id).change((e) => {
                const aircraft_id = $(select_id + " option:selected").val();
                console.log('aircraft select change: ', aircraft_id);

                $.ajax({
                    url: "{{ url('/pireps/fares') }}?aircraft_id=" + aircraft_id,
                    type: 'GET',
                    headers: {
                        'x-api-key': '{{ Auth::user()->api_key }}'
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
        });
    </script>
@endsection

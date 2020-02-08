@section('scripts')
  <script>
    $(document).ready(function () {
      $('#settings a').editable({
        type: 'text',
        mode: 'inline',
        emptytext: 'default',
        url: '/admin/settings/update',
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

      $(document).on('submit', 'form.rm_fare', function (event) {
        event.preventDefault();
        $.pjax.submit(event, '#aircraft_fares_wrapper', {push: false});
      });

      $(document).on('pjax:complete', function () {
        initPlugins();
      });
    });
  </script>
@endsection

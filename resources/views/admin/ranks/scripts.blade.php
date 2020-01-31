@section('scripts')
  <script>
    function setEditable() {

      const token = $('meta[name="csrf-token"]').attr('content');
      const api_key = $('meta[name="api-key"]').attr('content');

      @if(isset($rank))
      $('#subfleets-table a').editable({
        type: 'text',
        mode: 'inline',
        emptytext: 'inherited',
        url: '{{ url('/admin/ranks/'.$rank->id.'/subfleets') }}',
        title: 'Enter override value',
        ajaxOptions: {
          type: 'put',
          headers: {
            'x-api-key': api_key,
            'X-CSRF-TOKEN': token,
          }
        },
        params: function (params) {
          return {
            subfleet_id: params.pk,
            name: params.name,
            value: params.value
          }
        }
      });
      @endif
    }

    $(document).ready(function () {

      setEditable();

      $(document).on('submit', 'form.pjax_form', function (event) {
        event.preventDefault();
        $.pjax.submit(event, '#rank_subfleet_wrapper', {push: false});
      });

      $(document).on('pjax:complete', function () {
        initPlugins();
        setEditable();
      });
    });
  </script>
@endsection

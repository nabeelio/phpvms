@section('scripts')
  <script>
    'use strict';

    function setEditable() {
      const csrf_token = $('meta[name="csrf-token"]').attr('content');
      const api_key = $('meta[name="api-key"]').attr('content');

      @if(isset($airport))
      $('#airport-expenses a.text').editable({
        emptytext: '0',
        url: '{{ url('/admin/airports/'.$airport->id.'/expenses') }}',
        title: 'Enter override value',
        ajaxOptions: {
          type: 'post',
          headers: {
            'x-api-key': api_key,
            'X-CSRF-TOKEN': csrf_token
          }
        },
        params: function (params) {
          return {
            _method: 'put',
            expense_id: params.pk,
            name: params.name,
            value: params.value
          }
        }
      });

      $('#airport-expenses a.dropdown').editable({
        type: 'select',
        emptytext: '0',
        source: {!! json_encode(list_to_editable(\App\Models\Enums\ExpenseType::select())) !!},
        url: '{{ url('/admin/airports/'.$airport->id.'/expenses') }}',
        title: 'Enter override value',
        ajaxOptions: {
          type: 'post',
          headers: {
            'x-api-key': api_key,
            'X-CSRF-TOKEN': csrf_token
          }
        },
        params: function (params) {
          return {
            _method: 'put',
            expense_id: params.pk,
            name: params.name,
            value: params.value
          }
        }
      });
      @endif
    }

    $(document).ready(function () {
      const api_key = $('meta[name="api-key"]').attr('content');
      const csrf_token = $('meta[name="csrf-token"]').attr('content');

      setEditable();

      $('#airports-table a.inline').editable({
        type: 'text',
        mode: 'inline',
        emptytext: '0',
        url: '{{ url('/admin/airports/fuel') }}',
        title: 'Enter price per unit of fuel',
        ajaxOptions: {
          type: 'post',
          headers: {
            'x-api-key': api_key,
            'X-CSRF-TOKEN': csrf_token
          }
        },
        params: function (params) {
          return {
            _method: 'put',
            id: params.pk,
            name: params.name,
            value: params.value
          }
        }
      });

      $('a.airport_data_lookup').click(async function (e) {
        e.preventDefault();
        const icao = $("input#airport_icao").val();
        if (icao === '') {
          return;
        }

        let response;
        try {
          response = await phpvms.airport_lookup(icao);
        } catch (e) {
          console.log('Error looking up airport!', e);
          return;
        }

        _.forEach(response.data, function (value, key) {
          if (key === 'city') {
            key = 'location';
          }

          $("#" + key).val(value);

          if (key === 'tz') {
            $("#timezone").val(value);
            $("#timezone").trigger('change');
          }
        });
      });

      $(document).on('submit', 'form.modify_expense', function (event) {
        event.preventDefault();
        console.log(event);
        $.pjax.submit(event, '#airport-expenses-wrapper', {push: false});
      });

      $(document).on('pjax:complete', function () {
        initPlugins();
        setEditable();
      });
    });
  </script>
@endsection

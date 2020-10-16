@section('scripts')
  <script>
    function check() {
      let curr_model = $("#ref_model option:selected").text();
      $('#ref_models select').each(function () {
        $(this).attr('disabled', 'disabled');
      });
      $('#ref_model_' + curr_model).attr('disabled', false);
    }
    $(document).ready(function () {
      check();
      $(document).on('change', '#ref_model', function () {
        check();
      })
    })
  </script>
@endsection

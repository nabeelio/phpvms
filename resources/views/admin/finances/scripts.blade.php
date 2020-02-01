@section('scripts')
  <script>
    $(document).ready(() => {
      const select_id = "select#month_select";
      $(select_id).change((e) => {
        const date = $(select_id + " option:selected").val();
        const location = window.location.toString().split('?')[0];
        window.location = location + '?month=' + date;
      });
    });
  </script>
@endsection

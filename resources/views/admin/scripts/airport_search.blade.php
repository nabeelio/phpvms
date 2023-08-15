<script>
$(document).ready(function () {
  $("select.airport_search").select2({
    ajax: {
      url: '{{ Config::get("app.url") }}/api/airports/search',
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
          orderBy: 'id',
          sortedBy: 'asc'
        }
      },
      processResults: function (data, params) {
        if (!data.data) { return [] }
        const results = data.data.map(apt => {
          return {
            id: apt.id,
            text: apt.description
          }
        })

        const pagination = {
          more: data.meta.next_page !== null,
        }

        return {
          results,
          pagination,
        };
      },
      cache: true,
      dataType: 'json',
      delay: 250,
      minimumInputLength: 2,
    },
    width: 'resolve',
    placeholder: 'Type to search',
  });
});
</script>

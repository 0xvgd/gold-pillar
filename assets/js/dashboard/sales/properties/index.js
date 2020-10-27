import moment from "moment";

const datatable = $("#datatable");
const baseUrl = datatable.data("url");
const isAdmin = datatable.data("isAdmin");

let columns = [
  {
      data: 'mainPhoto',
      render: function (data, type, row) {
          return '<a class="#" href="' + data + '" data-lightbox="resource-' + row.id + '"><img src="' + data + '/thumb:30*30*outbound" ></a>';
      }
  },
  {
      data: 'name'
  },
  {
      data: 'price',
      render: function (data) {
          var formatter = new Intl.NumberFormat([], {
              style: 'currency',
              currency: data.currency
          });
          return formatter.format(data.amount);

      }
  },
  {
      data: 'hits'
  }
]

if (isAdmin) {
  columns.push({
      data: 'owner',
      render: function (data) {
          return data ? data.name : '--';
      }
  })
}

columns = columns.concat([
  {
      data: 'postStatus',
      render: function (data, type, row) {
          var span = '';
          if (data && data.value) {
              let estilo = ''
              switch (data.value) {
                  case 'approved':
                      estilo = " fa fa-check fa-2x c-green-500";
                      break;
                  case 'denied':
                      estilo = " fas fa-exclamation-triangle fa-2x c-red-500";
                      break;
                  case 'on_approval':
                      estilo = " fas fa-clock fa-2x c-orange-300";
                      break;   
              }
              span = '<span class="' + estilo + '" title="' + data.label + '"></span>';
          }
          return span;
      }
  }, {
      data: 'propertyStatus',
      render: function (data, type, row) {
          var span = '';
          if (data && data.value) {
              let estilo = '';
              switch (data.value) {
                  case 'for_sale':
                      estilo = "c-blue-500";
                      break;
                  case 'sold':
                      estilo = "c-green-500";
                      break;
                  case 'removed':
                      estilo = "c-red-500";
                      break;
              }
              span = '<span class="' + estilo + '" title="' + data.label + '"><b>' + data.label + '</b></span>';
          }
          return span;
      }
  }, {
    data: 'id',
    className: 'text-right',
    render(id) {
        return [
            `<a href="${baseUrl}${id}/finance" class="btn btn-default" title="Financial info"><span class="fas fa-calculator"></span></a>`,
            `<a href="${baseUrl}${id}" class="btn btn-default" title="Edit"><span class="fas fa-pen"></span></a>`,
        ].join(' ')
    }
}
])

datatable
  .dataTable({
    ajax: `${baseUrl}search.json`,
    processing: true,
    ordering: true,
    columnDefs: [
      {
        orderable: false,
        targets: "no-sort"
      }
    ],
    serverSide: true,
    searching: false,
    columns: columns
  })
  .on("preXhr.dt", (e, settings, data) => {
    data.search = $("#search-form").serialize();
  })
  .on("xhr.dt", (e, settings, data) => {
    $("[type=submit]").prop("disabled", false);
  });

$("#search-form").on("submit", () => {
  datatable.DataTable().ajax.reload();
  return false;
});
